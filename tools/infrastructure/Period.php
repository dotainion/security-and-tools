<?php

namespace tools\infrastructure;

class Period implements IObjects{
    protected Id $id;
    protected DateHelper $from;
    protected DateHelper $to;

    public function __construct(?Id $id=null, ?DateHelper $from=null, ?DateHelper $to=null){
        isset($id) && $this->setId($id);
        isset($from) && $this->setFrom($from);
        isset($to) && $this->setTo($to);
    }

    public function id():IId{
        return $this->id;
    }

    public function to():DateHelper{
        return $this->to;
    }

    public function from():DateHelper{
        return $this->from;
    }

    public function setId(Id $id):void{
        $this->id = $id;
    }

    public function setTo(DateHelper $to):void{
        $this->to = $to;
    }

    public function setFrom(DateHelper $from):void{
        $this->from = $from;
    }
}
