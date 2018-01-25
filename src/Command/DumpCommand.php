<?php

/*
 * This file is part of the NelmioApiDocBundle.
 *
 * (c) Nelmio <hello@nelm.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akuma\Component\ApiDoc\Command;

use Akuma\Component\ApiDoc\Annotation\ApiDoc;
use Akuma\Component\ApiDoc\Util\ApiDocHelper;
use Akuma\Component\ApiDoc\Formatter\HtmlFormatter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouteCollection;

class DumpCommand extends Command
{
    /**
     * @var array
     */
    protected $availableFormats = array('markdown', 'json', 'html');

    /** @var RouteCollection */
    protected $routeCollection;

    /**
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteCollection $routeCollection)
    {
        parent::__construct();

        $this->routeCollection = $routeCollection;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Dumps API documentation in various formats')
            ->addOption(
                'format',
                '',
                InputOption::VALUE_REQUIRED,
                'Output format like: ' . implode(', ', $this->availableFormats),
                $this->availableFormats[0]
            )
            ->addOption('view', '', InputOption::VALUE_OPTIONAL, '', ApiDoc::DEFAULT_VIEW)
            ->addOption('no-sandbox', '', InputOption::VALUE_NONE)
            ->setName('api:doc:dump');
    }

    /**
     * @return ApiDocHelper
     */
    protected function getApiDocHelper()
    {
        $helperSet = $this->getHelperSet();
        if (!$helperSet->has(ApiDocHelper::NAME)) {
            $helperSet->set(new ApiDocHelper());
        }

        return $this->getHelper(ApiDocHelper::NAME);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $format = $input->getOption('format');
        $view = $input->getOption('view');

        if (!in_array($format, $this->availableFormats)) {
            throw new \RuntimeException(sprintf('Format "%s" not supported.', $format));
        }

        $formatter = $this->getApiDocHelper()->getFormatter($format);

        if ($input->getOption('no-sandbox') && 'html' === $format) {
            /** @var HtmlFormatter $formatter */
            $formatter->setEnableSandbox(false);
        }

        $extractor = $this->getApiDocHelper()->getApiDocExtractor();
        $extractor->setRouteCollection($this->routeCollection);
        $extractedDoc = $extractor->all($view);
        $formattedDoc = $formatter->format($extractedDoc);

        if ('json' === $format) {
            $output->writeln(json_encode($formattedDoc));
        } else {
            $output->writeln($formattedDoc, OutputInterface::OUTPUT_RAW);
        }
    }
}
