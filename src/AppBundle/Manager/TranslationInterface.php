<?php

namespace AppBundle\Manager;

/**
 * Translation interface.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
interface TranslationInterface
{
    /**
     * @return string
     */
    public function getLocale();

    /**
     * @return string
     */
    public function getContent();
}
