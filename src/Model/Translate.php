<?php


namespace Int\Lumen\Core\Model;


/**
 * Traits Translate
 * @package Int\Lumen\Core\Model\Scopes
 */
trait Translate
{

    protected $language = 'all';

    protected $translations = [];


    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        $this->translate();
    }


    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }


    /**
     * Translate
     */
    private function translate()
    {

        if (empty($this->translationAttributes)) {
            return;
        }

        if (empty($this->translations)) {
            $this->fillTranslations();
        }

        foreach ($this->translationAttributes as $attribute) {
            if ($this->getLanguage() == 'all') {
                $this->attributes[$attribute] = $this->translations[$attribute];
                continue;
            }

            $this->attributes[$attribute] = $this->translations[$attribute][$this->getLanguage()] ?? current($this->translations[$attribute]);
        }
    }


    /**
     * Fill Translations
     */
    private function fillTranslations()
    {
        foreach ($this->translationAttributes as $attribute) {
            $this->translations[$attribute] = $this->attributes[$attribute];
        }
    }


}