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

    public $translationInfo = [];


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
            if (!isset($this->attributes[$attribute])) {
                continue;
            }

            if (!isset($this->attributes[$attribute])) {
                continue;
            }

            if ($this->getLanguage() == 'all') {
                $this->attributes[$attribute] = $this->translations[$attribute];
                continue;
            }

            // $this->attributes[$attribute] =
            //     isset($this->translations[$attribute][$this->getLanguage()])
            //     && $this->translations[$attribute][$this->getLanguage()] != ''
            //         ? $this->translations[$attribute][$this->getLanguage()]
            //         : current($this->translations[$attribute]);

            $this->attributes[$attribute] = $this->getTranslate($attribute);

        }
    }


    private function getTranslate($attribute)
    {
        if (isset($this->translations[$attribute][$this->getLanguage()])
            && $this->translations[$attribute][$this->getLanguage()] != '') {
            return $this->translations[$attribute][$this->getLanguage()];
        }

        foreach ($this->translations[$attribute] as $lang => $translate) {
            if (is_string($translate) && $translate !== '' ) {
                $this->translationInfo[$attribute] = $lang;
                return $translate;
            }

            if (is_array($translate) && $translate ) {
                $this->translationInfo[$attribute] = $lang;
                return $translate;
            }
        }

        return '';
    }


    /**
     * Fill Translations
     */
    private function fillTranslations()
    {
        foreach ($this->translationAttributes as $attribute) {
            if (!isset($this->attributes[$attribute])) {
                continue;
            }


            $this->translations[$attribute] = $this->attributes[$attribute];
        }
    }
}