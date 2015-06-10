<?php
namespace DbDocCreator\Command;

use Console\Helper\QuestionHelper;
use DbDocCreator\Generate\Generate;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class GenerateCommand
 * @author Leo Yang <897798676@qq.com>
 */
class DbDocCreator extends Command
{


    /**
     * @var array
     */
    static $DB_TYPES = array(
        'pdo_mysql',
        'pdo_sqlite',
        'pdo_pgsql',
        'pdo_oci',
        'oci8',
        'ibm_db2',
        'pdo_ibm',
        'pdo_sqlsrv',
        'mysqli',
        'drizzle_pdo_mysql',
        'sqlsrv'
    );

    /**
     * @param string $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('generate:database:doc')
            ->setDescription('根据数据库注释信息生成文档')
        ;
    }

    /**
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $helperSet->set(new QuestionHelper());
        parent::setHelperSet($helperSet);
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paramters = $this->getParamters($output);

        $paramters['charset'] = 'utf8';

        $conn      = DriverManager::getConnection($paramters, new Configuration());
        $gnerate   = new Generate($conn, $output);
        $gnerate->output($paramters['outputdir']);
    }

    /**
     * @param OutputInterface $output
     * @return array
     */
    protected function getParamters(OutputInterface $output)
    {
        $host      = $port = $user = $password = $dbname = null;
        $outputdir = "./";

        /** @var QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');
        $driver = $helper->askAndValid($output, $this->getDbTypeQuestion());

        if ($driver == 'pdo_sqlite') {
            $path = $helper->askAndValid($output, $this->getSqlitePathQuestion());
        } else {
            $host      = $helper->askAndValid($output, $this->getHostQuestion());
            $port      = $helper->askAndValid($output, $this->getPortQuestion());
            $user      = $helper->askAndValid($output, $this->getUserQuestion());
            $password  = $helper->askAndValid($output, $this->getPassQuestion());
            $dbname    = $helper->askAndValid($output, $this->getDbnameQuestion());
            $outputdir = $helper->askAndValid($output, $this->getOutputQuestion());
        }

        return compact('driver', 'host', 'port', 'user', 'password', 'dbname', 'path', 'outputdir');
    }


    protected function getOutputQuestion()
    {
        $question = new Question('请指定文档输出目录:(<info>./</info>):', './');

        return $question;
    }


    /**
     * @return Question
     */
    protected function getHostQuestion()
    {
        $question = new Question('请输入host:(<info>localhost</info>):', 'localhost');

        return $question;
    }

    /**
     * @return Question
     */
    protected function getDbTypeQuestion()
    {
        $question = new Question('请选择数据库驱动(<info>pdo_mysql</info>):', 'pdo_mysql');
        $question->setAutocompleterValues(static::$DB_TYPES);
        $question->setValidator(function ($value) {
            if (!in_array($value, static::$DB_TYPES)) {
                throw new InvalidArgumentException(sprintf("数据库驱动错误,只能为(%s)", implode(',', static::$DB_TYPES)));
            }
        });

        return $question;
    }

    /**
     * @return Question
     */
    private function getPortQuestion()
    {
        $question = new Question('请输入数据库端口(<info>null</info>):', null);
        $question->setValidator(function ($value) {
            if (!empty($value) && !is_numeric($value)) {
                throw new InvalidArgumentException("数据库端口错误,只能为数字");
            }
        });

        return $question;
    }

    /**
     * @return Question
     */
    private function getPassQuestion()
    {
        $question = new Question('请输入密码(<info>null</info>):', '');

        return $question;
    }

    /**
     * @param array $dbs
     * @return Question
     */
    private function getDbnameQuestion(array $dbs = array())
    {
        $question = new Question('请输入数据库名称:');
        $question->setAutocompleterValues($dbs);
        $question->setValidator(function ($value) {
            if (empty($value)) {
                throw new InvalidArgumentException("数据库名不能为空");
            }
        });

        return $question;
    }

    /**
     * @return Question
     */
    private function getUserQuestion()
    {
        $question = new Question('请输入用户名(<info>root</info>):', 'root');
        $question->setValidator(function ($value) {
            if (empty($value)) {
                throw new InvalidArgumentException("用户名不能为空");
            }
        });

        return $question;
    }

    /**
     * @return Question
     */
    private function getSqlitePathQuestion()
    {
        $question = new Question('请输入sqlite数据库路径:');
        $question->setValidator(function ($value) {
            if (!file_exists($value)) {
                throw new InvalidArgumentException("数据库文件不存在");
            }
        });

        return $question;
    }
}