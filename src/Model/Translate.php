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

        $this->translationInfo['full_translation'] = true;

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

    /**
     * @todo refactoring thism altera a verificação se o campo é um array pelo atribute de cast
     *
     */
    private function getTranslate($attribute)
    {

        $currentAttr = $this->translations[$attribute];

   
      
       // if (!is_array($this->translations[$attribute][$this->getLanguage()])) {
        if (isset($currentAttr[$this->getLanguage()])
            && ((is_string($currentAttr[$this->getLanguage()]) && $currentAttr[$this->getLanguage()] != '')
            || (is_array($currentAttr[$this->getLanguage()]) && count($currentAttr[$this->getLanguage()]) > 0))) {

            return $currentAttr[$this->getLanguage()];
        }

        foreach ($currentAttr as $lang => $translate) {
            if (is_string($translate) && $translate !== '') {
                $this->translationInfo[$attribute] = $lang;
                $this->translationInfo['full_translation'] = false;
                return $translate;
            }

            if (is_array($translate) && $translate) {
                $this->translationInfo[$attribute] = $lang;
                $this->translationInfo['full_translation'] = false;
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