<?php

/**
 * FanPress CM 4.x
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */

namespace fpcm\model\pubtemplates;

/**
 * Share button template object
 * 
 * @package fpcm\model\system
 * @author Stefan Seehafer <sea75300@yahoo.de>
 * @copyright (c) 2011-2018, Stefan Seehafer
 * @license http://www.gnu.org/licenses/gpl.txt GPLv3
 */
final class sharebuttons extends template {

    const TEMPLATE_ID = 'shareButtons';

    /**
     * Template-Platzhalter
     * @var array
     */
    protected $replacementTags = [
        '{{facebook}}' => '',
        '{{twitter}}' => '',
        '{{googlePlus}}' => '',
        '{{tumblr}}' => '',
        '{{pinterest}}' => '',
        '{{reddit}}' => '',
        '{{whatsapp}}' => '',
        '{{email}}' => '',
        '{{link}}' => '',
        '{{description}}' => '',
        '{{credits}}' => ''
    ];
    
    /**
     * zu teilender Link
     * @var string
     */
    protected $link;

    /**
     * Beschreibung für Share-EIntrag
     * @var string
     */
    protected $description;

    /**
     * Konstruktor
     * @param string $link Artikel-Link
     * @param string $description Artikel-Beschreibung
     */
    public function __construct($fileName = null)
    {
        if (!$fileName) {
            $fileName = 'sharebuttons';
        }

        parent::__construct('common' . DIRECTORY_SEPARATOR . $fileName . '.html');
    }
    
    /**
     * Share-Buttons parsen
     * @return string
     */
    public function parse()
    {
        if (!$this->config->system_show_share) {
            return '';
        }

        $content = "<!-- Start FanPress CM Share Buttons -->".PHP_EOL.$this->content.PHP_EOL."<!-- Stop FanPress CM Share Buttons -->";
        foreach ($this->initTags() as $replacement => $value) {
            
            if (!is_array($value)) {
                $content = str_replace($replacement, $value, $content);
                continue;
            }

            $value['icon'] = \fpcm\classes\dirs::getDataUrl(\fpcm\classes\dirs::DATA_SHARE, $value['icon']);

            $dataStr = '';
            if (count($value['data'])) {
                foreach ($value['data'] as $key => $dataValue) {
                    $dataStr .= 'data-'.$key.'="'.$dataValue.'"';
                }
            }

            $content = str_replace($replacement, "<a href=\"{$value['link']}\" {$dataStr}><img src=\"{$value['icon']}\" alt=\"{$value['text']}\"></a>", $content);
        }

        return $content;
    }
    
    /**
     * 
     * @param string $link
     * @param string $description
     * @return bool
     */
    public function assignData(string $link, string $description) : bool
    {
        $this->link = rawurlencode($link);
        $this->description = $description;
        return true;
    }

    /**
     * 
     * @return array
     */
    private function initTags() : array
    {
        return $this->events->trigger('pub\parseShareButtons', array_merge($this->replacementInternal, [
            '{{facebook}}' => [
                'link' => "https://www.facebook.com/sharer/sharer.php?u={$this->link}&amp;t={$this->description}",
                'icon' => "default/facebook.png",
                'text' => "Facebook",
                'data' => []
            ],
            '{{twitter}}' => [
                'link' => "https://twitter.com/intent/tweet?source={$this->link}&amp;text={$this->description}",
                'icon' => "default/twitter.png",
                'text' => "Twitter",
                'data' => []
            ],
            '{{googlePlus}}' => [
                'link' => "https://plus.google.com/share?url={$this->link}",
                'icon' => "default/googleplus.png",
                'text' => "Google+",
                'data' => []
            ],
            '{{tumblr}}' => [
                'link' => "http://www.tumblr.com/share?v=3&amp;u={$this->link}&amp;t={$this->description}&amp;s=",
                'icon' => "default/tumblr.png",
                'text' => "Share on Tumblr",
                'data' => []
            ],
            '{{pinterest}}' => [
                'link' => "http://pinterest.com/pin/create/button/?url={$this->link}&amp;description={$this->description}",
                'icon' => "default/pinterest.png",
                'text' => "Pin it",
                'data' => []
            ],
            '{{reddit}}' => [
                'link' => "http://www.reddit.com/submit?url={$this->link}&amp;title={$this->description}",
                'icon' => "default/reddit.png",
                'text' => "Submit to Reddit",
                'data' => []
            ],
            '{{whatsapp}}' => [
                'link' => "whatsapp://send?text={$this->description}: {$this->link}",
                'icon' => "default/whatsapp.png",
                'text' => "Share on WhatsApp",
                'data' => [
                    'action' => 'share/whatsapp/share'
                ]
            ],
            '{{email}}' => [
                'link' => "mailto:?subject={$this->description}&amp;body={$this->link}",
                'icon' => "default/email.png",
                'text' => "Share via E-Mail",
                'data' => []
            ],
            '{{link}}' => $this->link,
            '{{description}}' => $this->description,
            '{{credits}}' => "<!-- Icon set powered by http://simplesharingbuttons.com and https://whatsappbrand.com/ -->"
        ]));
    }

}

?>