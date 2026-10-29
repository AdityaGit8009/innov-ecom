<?php
class SEO
{
    private $title = 'SIP Innovation - Software Projects & Training';
    private $description = 'Get the best software projects, mini-projects, and training from SIP Innovation.';
    private $canonicalUrl = '';
    private $structuredData = [];

    public function __construct()
    {
        $this->canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function setMeta($title, $description)
    {
        if (!empty($title)) $this->title = $title . ' - SIP Innovation';
        if (!empty($description)) $this->description = $description;
    }

    public function addStructuredData($data)
    {
        $this->structuredData[] = $data;
    }

    public function renderTags()
    {
        $html = "<title>" . htmlspecialchars($this->title) . "</title>\n";
        $html .= "    <meta name=\"description\" content=\"" . htmlspecialchars($this->description) . "\">\n";
        $html .= "    <link rel=\"canonical\" href=\"" . htmlspecialchars($this->canonicalUrl) . "\">\n";

        if (!empty($this->structuredData)) {
            $html .= "    <script type=\"application/ld+json\">\n";
            $html .= json_encode($this->structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $html .= "\n    </script>\n";
        }

        return $html;
    }
}
