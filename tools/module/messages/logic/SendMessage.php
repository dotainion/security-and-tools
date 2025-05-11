<?php
namespace tools\module\login\logic;

use InvalidArgumentException;
use Pusher\Pusher;
use tools\infrastructure\Assert;
use tools\infrastructure\Env;

class SendMessage{
    protected Pusher $pusher;

    public function __construct(){
        $this->assertRequirements();
        $this->pusher = new Pusher(
            Env::pusherAppKey(),
            Env::pusherAppSecret(),
            Env::pusherAppId(),
            [
                'cluster' => Env::pusherAppCluster(),
                'useTLS' => true
            ]
        );
    }

    public function assertRequirements():bool{
        Assert::stringNotEmpty(Env::pusherAppKey());
        Assert::stringNotEmpty(Env::pusherAppSecret());
        Assert::stringNotEmpty(Env::pusherAppId());
        Assert::stringNotEmpty(Env::pusherAppCluster());
        return true;
    }

    public function send(string $channel, string $event, string $message):void{
        if (empty($message)) {
            throw new InvalidArgumentException('Message not provided.');
        }
        $this->pusher->trigger($channel, $event, ['message' => $message]);
    }
}