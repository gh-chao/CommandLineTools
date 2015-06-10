<?php
namespace Console\Helper;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper as BaseQuesionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class MyQuestionHelper
 * @author Leo Yang <897798676@qq.com>
 */
class QuestionHelper extends BaseQuesionHelper
{
    /**
     * @param OutputInterface $output
     * @param Question        $question
     * @return bool|mixed|null|string
     */
    public function askAndValid(OutputInterface $output, Question $question)
    {
        /** @var FormatterHelper $formatterHelper */
        $formatterHelper = $this->getHelperSet()->get('formatter');
        $validator       = $question->getValidator();

        while (true) {
            try {
                $value = $this->doAsk($output, $question);
                if ($validator) {
                    call_user_func($question->getValidator(), $value);
                }

                return $value;
            } catch (\InvalidArgumentException $e) {
                $output->writeln($formatterHelper->formatBlock($e->getMessage(), 'bg=red;fg=white', true));
            }
        }

        return false;
    }
}