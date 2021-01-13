<?php namespace tools;


// Un wrapper sur DocumentDom pour en simplifier l'usage
class DOMDocumentWrapper {
    private \DOMDocument $dom;

    public function __construct(string $body) {
        $this->dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $this->dom->loadHTML($body);
        libxml_use_internal_errors(false);
    }

    // Retourne la liste de tags dont le nom est $tag_name
    public function find($tag_name): \DOMNodeList {
        return $this->dom->getElementsByTagName($tag_name);
    }

    // retourne le premier tage dont le nom est $tag_name
    public function find_one($tag_name): ?\DOMNode {
        $node_list = $this->find($tag_name);

        if (count($node_list)) {
            return $node_list->item(0);
        }
        return null;
    }
}