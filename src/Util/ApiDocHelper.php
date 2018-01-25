<?php

namespace Akuma\Component\ApiDoc\Util;

use Akuma\Component\ApiDoc\Extractor\AnnotationsProviderInterface;
use Akuma\Component\ApiDoc\Extractor\ApiDocExtractor;
use Akuma\Component\ApiDoc\Extractor\Handler\PhpDocHandler;
use Akuma\Component\ApiDoc\Extractor\HandlerInterface;
use Akuma\Component\ApiDoc\Formatter\AbstractFormatter;
use Akuma\Component\ApiDoc\Formatter\HtmlFormatter;
use Akuma\Component\ApiDoc\Formatter\MarkdownFormatter;
use Akuma\Component\ApiDoc\Formatter\SimpleFormatter;
use Akuma\Component\ApiDoc\Twig\Extension\MarkdownExtension;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Templating\TemplateNameParser;

class ApiDocHelper extends Helper
{
    const NAME = 'api_doc_helper';

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return ApiDocExtractor
     */
    public function getApiDocExtractor()
    {
        $extractor = new ApiDocExtractor(
            $this->getAnnotationReader(),
            $this->getDocCommentExtractor(),
            $this->getHandlers(),
            $this->getAnnotationsProviders()
        );

        return $extractor;
    }

    /**
     * @param $name
     *
     * @return AbstractFormatter
     */
    public function getFormatter($name = 'json')
    {
        switch ($name) {
            case 'html':
                $formatter = new HtmlFormatter();
                $formatter->setTemplatingEngine($this->getTemplateEngine());

                return $formatter;
                break;
            case 'markdown':
                return new MarkdownFormatter();
            case 'json':
            default:
                return new SimpleFormatter();
                break;
        }
    }

    /**
     * @return DocCommentExtractor
     */
    public function getDocCommentExtractor()
    {
        return new DocCommentExtractor();
    }

    /**
     * @return AnnotationReader
     */
    public function getAnnotationReader()
    {
        return new AnnotationReader();
    }

    /**
     * @return array|HandlerInterface[]
     */
    public function getHandlers()
    {
        return [
            new PhpDocHandler($this->getDocCommentExtractor()),
        ];
    }

    /**
     * @return array|AnnotationsProviderInterface[]
     */
    public function getAnnotationsProviders()
    {
        return [];
    }

    /**
     * @return TwigEngine
     */
    protected function getTemplateEngine()
    {
        $reflected = new \ReflectionClass(__CLASS__);
        $path = dirname($reflected->getFileName(), 2) . '/Resources/views';
        $loader = new \Twig_Loader_Chain(array(
            new \Twig_Loader_Filesystem($path),
        ));

        if (class_exists('\Twig\Environment')) {
            $twig = new \Twig\Environment($loader);
        } else {
            $twig = new \Twig_Environment($loader);
        }

        $twig->addExtension(new MarkdownExtension());
        $templating = new TwigEngine($twig, new TemplateNameParser());

        return $templating;
    }
}
