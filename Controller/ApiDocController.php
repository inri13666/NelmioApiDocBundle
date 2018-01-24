<?php

namespace Nelmio\ApiDocBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;

class ApiDocController
{
    /**
     * @param string $view
     *
     * @return Response
     */
    public function indexAction($view = ApiDoc::DEFAULT_VIEW)
    {
        $extractedDoc = $this->get('nelmio_api_doc.extractor.api_doc_extractor')->all($view);
        $htmlContent = $this->get('nelmio_api_doc.formatter.html_formatter')->format($extractedDoc);

        return new Response($htmlContent, 200, array('Content-Type' => 'text/html'));
    }
}
