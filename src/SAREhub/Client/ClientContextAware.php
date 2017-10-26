<?php

namespace SAREhub\Client;


interface ClientContextAware
{

    /**
     * @return ClientContext
     */
    public function getClientContext();

    /**
     * @param ClientContext $context
     */
    public function setClientContext(ClientContext $context);
}