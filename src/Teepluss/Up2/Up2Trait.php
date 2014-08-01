<?php namespace Teepluss\Up2;

trait Up2Trait {

    /**
     * Attachment relation.
     *
     * @return Attachment
     */
    public function attachments()
    {
        $attachmentsModel = \Config::get('up2::attachments.model');

        return $this->morphToMany($attachmentsModel, 'attachmentable');
    }

}