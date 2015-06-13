<?php
namespace DbDocCreator;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Generate
 *
 * @author Leo Yang <897798676@qq.com>
 */
class Render
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @param Connection      $connection
     * @param OutputInterface $output
     */
    function __construct(Connection $connection, OutputInterface $output)
    {
        $this->connection = $connection;
        $this->output     = $output;
        $connection->getConfiguration()->getSQLLogger();
    }

    /**
     * @param $path
     */
    public function output($path, $dbname)
    {
        $sm     = $this->connection->getSchemaManager();
        $tables = $sm->listTables();

        $filename = is_dir($path) ? rtrim($path, "\\/") . "/{$dbname}.html" : $path;
        $html     = $this->_render($dbname, $tables);
        file_put_contents($filename, $html);
        $this->output->writeln("<info>生成完成</info>");
        $this->output->writeln(sprintf('文档已输出到: <info>%s</info>', $filename));
    }


    /**
     * @param $dbname
     * @param $tables
     * @return string
     */
    public function _render($dbname, $tables)
    {
        $loader   = new \Twig_Loader_Filesystem(__DIR__ . '/Resource');
        $twig     = new \Twig_Environment($loader, []);
        $template = $twig->loadTemplate('doc.html.twig');

        return $template->render(array(
            'dbname' => $dbname,
            'tables' => $tables,
        ));
    }

}