<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Service\ParserService\Parser;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\KafkaController;

#[AsCommand(
    name: 'app:read-file',
    description: 'Reading a file with data of products',
)]
class ReadFileCommand extends Command
{
    private Parser $parser;
    private MessageBusInterface $bus;

    public function __construct(Parser $parser, MessageBusInterface $bus)
    {
        $this->parser = $parser;
        $this->bus = $bus;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('url', InputArgument::OPTIONAL, 'URL for reading from a cloud')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');

        if (!$url) {
            $url = $io->ask('Пожайлуста, введите адрес для чтения файла');
        }

        try {

            $this->parser->setUrl($url);
            $products = $this->parser->getData();
            $serializedProducts = json_encode($products, JSON_UNESCAPED_UNICODE);

            $request = Request::createFromGlobals();
            $request->query->set('incomingProducts', $serializedProducts);

            (new KafkaController())->index($request, $this->bus);
            
            $io->success('Данные успешно отправлены в контроллер');

            return Command::SUCCESS;

        } catch (\Throwable $th) {

            $io->error('Ошибка при попытке отправить данные из команды: ' . $th->getMessage());
            return Command::FAILURE;

        }
    }
}
