<?php

use Illuminate\Database\Migrations\Migration;

class CreateAttachmentablesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachmentables', function($table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('attachment_id', 100);
            $table->string('attachmentable_type', 100);
            $table->integer('attachmentable_id')->unsigned();

            $table->index('attachment_id');
            $table->index(array('attachmentable_id', 'attachmentable_type'));

            $table->foreign('attachment_id')
                   ->references('id')->on('attachments')
                   ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachmentables', function($table)
        {
            $table->dropForeign('attachmentables_attachment_id_foreign');
        });

        Schema::dropIfExists('attachmentables');
    }

}