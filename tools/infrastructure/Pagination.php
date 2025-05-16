<?php
namespace tools\infrastructure;

class Pagination{
    protected ?int $limit;
    protected ?int $offset;

    public function __construct(?int $limit = null, ?int $offset = null){
        $this->set($limit, $offset);
    }

    public function set(?int $limit = null, ?int $offset = null){
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function limit():?int{
        return $this->limit;
    }

    public function offset():?int{
        return $this->offset;
    }

    public function get():array{
        return [
            'limit' => $this->limit(), 
            'offset' => $this->offset()
        ];
    }
}