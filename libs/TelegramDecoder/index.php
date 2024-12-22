<?php

namespace Libs\TelegramDecoder;

use Libs\Bot\Bot;

class TelegramResponse {
    public Message $message;
    public User $user;
    public Chat $chat;

    public function __construct($data) {
        // چک کردن اینکه آیا پیام شامل داده‌های مربوط به پیام‌هاست
        if (isset($data['message'])) {
            $this->message = new Message($data['message']);
        }
        if(isset($data["callback_query"])){
            $this->callback_query = new CallbackQuery($data["callback_query"]);
        }
    }
}

class CallbackQuery{
    public $id;
    public User $from;
    public Message $message;
    public $chat_instance;
    public $data;
    public function __construct($update) {
        $this->id = $update["id"];
        $this->from = new User($update["from"]);
        $this->message = new Message($update["message"]);
        $this->chat_instance = $update["chat_instance"];
        $this->data = $update["data"];
    }
}

// کلاس برای پیام‌ها
class Message {
    public $message_id;
    public $text;
    public $from;
    public $chat;
    public $photo;
    public $video;
    public $document;
    public $reply_to_message; // پیام ریپلای شده
    public $forward_from; // پیام فروارد شده
    public $forward_date; // تاریخ فروارد

    public function __construct($data) {
        $this->message_id = $data['message_id'];
        $this->text = $data['text'] ?? null;
        $this->from = new User($data['from']);
        $this->chat = new Chat($data['chat']);
        
        // بررسی اینکه پیام شامل عکسی هست یا نه
        if (isset($data['photo'])) {
            $this->photo = new Photo($data['photo'][0]);
        }
        
        // بررسی اینکه پیام شامل ویدئو هست یا نه
        if (isset($data['video'])) {
            $this->video = new Video($data['video']);
        }

        // بررسی اینکه پیام شامل سند (فایل) هست یا نه
        if (isset($data['document'])) {
            $this->document = new Document($data['document']);
        }

        // اگر پیام ریپلای شده باشد
        if (isset($data['reply_to_message'])) {
            $this->reply_to_message = new Message($data['reply_to_message']);
        }

        // اگر پیام فروارد شده باشد
        if (isset($data['forward_from'])) {
            $this->forward_from = new User($data['forward_from']);
        }

        // تاریخ فروارد پیام
        if (isset($data['forward_date'])) {
            $this->forward_date = $data['forward_date'];
        }
    }

    public function reply_text($text, $reply_keyboard = null){
        Bot::sendMessage($this->from->id, $text, $this->message_id, $reply_keyboard);
        error_log("132");
    }
}

// کلاس برای کاربران
class User {
    public $id;
    public $first_name;
    public $last_name;
    public $username;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->username = $data['username'] ?? null;
    }

    public function sendMessage($text, $reply = null, $reply_keyboard = null){
        Bot::sendMessage($this->id, $text, $reply, $reply_keyboard);
    }
}

// کلاس برای چت‌ها
class Chat {
    public $id;
    public $type;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->type = $data['type'];
    }

    public function sendMessage($text, $reply = null){
        Bot::sendMessage($this->id, $text, $reply);
    }
}

// کلاس برای عکس‌ها
class Photo {
    public $file_id;
    public $file_size;
    public $width;
    public $height;

    public function __construct($data) {
        $this->file_id = $data['file_id'];
        $this->file_size = $data['file_size'] ?? null;
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
    }
}

// کلاس برای ویدئوها
class Video {
    public $file_id;
    public $duration;
    public $width;
    public $height;

    public function __construct($data) {
        $this->file_id = $data['file_id'];
        $this->duration = $data['duration'] ?? null;
        $this->width = $data['width'] ?? null;
        $this->height = $data['height'] ?? null;
    }
}

// کلاس برای فایل‌ها
class Document {
    public $file_id;
    public $file_size;
    public $file_name;

    public function __construct($data) {
        $this->file_id = $data['file_id'];
        $this->file_size = $data['file_size'] ?? null;
        $this->file_name = $data['file_name'] ?? null;
    }
}
?>