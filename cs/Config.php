<?php

namespace mister42\cs;

/**
 * Basic rules.
 */
class Config extends \PhpCsFixer\Config
{
    /**
     * {@inheritdoc}
     */
    public function __construct($name = 'mr42-cs-config')
    {
        parent::__construct($name);

        $this->setRiskyAllowed(true);

        $this->setRules([
            '@PSR2' => true,
        ]);
    }
}
