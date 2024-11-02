<?php
namespace tools\infrastructure;

class BBCode{
    protected string $content = '';
    protected array $code = [
        '<' => '[',
        '>' => ']',
        ';' => '=+=',
    ];

    public function __construct(?string $content=null){
        $content !== null && $this->setContent($content);
    }

    public function setContent(string $content):self{
        $this->content = $content;
        return $this;
    }

    public function bbCode():string{
        $content = $this->content;
        foreach($this->code as $tag => $code){
            $content = str_replace($tag, $code, $content);
        }
        return $content;
    }

    public function html():string{
        $content = $this->content;
        foreach($this->code as $tag => $code){
            $content = str_replace($code, $tag, $content);
        }
        return $content;
    }
}
