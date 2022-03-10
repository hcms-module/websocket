<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateWebsocketMsgTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('websocket_msg', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('msg_id', 64)
                ->default('')
                ->nullable(false)
                ->comment('消息id，随机字符串');
            $table->string('type', 32)
                ->default('text')
                ->nullable(false)
                ->comment('消息类型，例：text文本、image图片、video视频、event事件等');
            $table->string('user_target', 128)
                ->default('')
                ->nullable(false)
                ->comment('发送用户标识，唯一确认发送用户的标识。');
            $table->string('room', 128)
                ->default('')
                ->nullable(false)
                ->comment('所属房间');
            $table->string('msg_content', 1024)
                ->default('')
                ->nullable(false)
                ->comment('消息内容，建议不要过长。如果有其他信息建议创建附表。允许使用json格式');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websocket_msg');
    }
}
