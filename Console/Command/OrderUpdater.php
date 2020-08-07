<?php
declare(strict_types=1);

namespace Martinshaw\CLIOrderUpdater\Console\Command;

use Martinshaw\CLIOrderUpdater\Api\OrderRetrieverInterface;
use Martinshaw\CLIOrderUpdater\Api\OrderUpdaterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;


class OrderUpdater extends Command
{
    const SUCCESS_CODE = 0;
    const ERROR_CODE = 1;
    const IDENTIFIER = 'identifier';

    /**
     * @var OrderRetrieverInterface
     */
    private $orderRetriever;
    /**
     * @var OrderUpdaterInterface
     */
    private $orderUpdater;

    /**
     * OrderUpdater constructor.
     * @param OrderRetrieverInterface $orderRetriever
     * @param OrderUpdaterInterface $orderUpdater
     * @param string|null $name
     */
    public function __construct(
        OrderRetrieverInterface $orderRetriever,
        OrderUpdaterInterface $orderUpdater,
        string $name = null
    ) {
        parent::__construct($name);
        $this->orderRetriever = $orderRetriever;
        $this->orderUpdater = $orderUpdater;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('martinshaw:orderupdate');
        $this->setDescription('Updates orders with a supplied email address based on an old email address or an order ID.');
        $this->addArgument(self::IDENTIFIER, InputArgument::REQUIRED, 'Order ID or previous email address to identify orders to update.');
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get any orders which match our supplied criteria and render them for the user
        $identifier = $input->getArgument(self::IDENTIFIER);
        $orders = $this->orderRetriever->execute($identifier);
        if (empty($orders)) {
            $output->writeln('<error>No orders found.</error>');
            return self::ERROR_CODE;
        }
        $this->outputSearchResults($output, $orders);

        // Get the new email address, with which to update the order records.
        $questionHelper = $this->getHelper('question');
        $question = new Question('Input new customer email address to update order records: ');
        $customerEmail = $questionHelper->ask($input, $output, $question);
        if ($error = $this->validateCustomerEmail($customerEmail)) {
            $output->writeln('<error>' . $error . '</error>');
            return self::ERROR_CODE;
        }

        // Update the orders with the validated email
        $this->orderUpdater->execute($orders, $customerEmail);

        // All done, report the success to the user
        $output->writeln('<info>Success.</info>');
        return self::SUCCESS_CODE;
    }

    /**
     * Verify that the input that the user has given is a valid email address.
     *
     * @param $email
     * @return string
     */
    protected function validateCustomerEmail($email)
    {
        $error = '';
        if (!$email) {
            $error = 'No customer email address supplied';
        }
        if ($email && !\Zend_Validate::is($email, 'EmailAddress')) {
            $error = 'Supplied customer email address is not a valid email address';
        }

        return $error;
    }

    /**
     * Take the array of orders we've retrieved and output them in a tabular format for the user to review
     *
     * @param OutputInterface $output
     * @param array $orders
     */
    protected function outputSearchResults(OutputInterface $output, array $orders): void
    {
        $output->writeln('<info>Found ' . count($orders) . ' matching orders. </info>');
        $tableRows = [];
        foreach ($orders as $order) {
            $tableRows [] = [
                $order->getEntityId(),
                $order->getIncrementId(),
                $order->getCustomerEmail(),
                $order->getGrandTotal()
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['Entity ID', 'Increment ID', 'Customer Email', 'Grand Total'])
            ->setRows($tableRows);
        $table->render();
    }
}
