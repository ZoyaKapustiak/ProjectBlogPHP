<?php

namespace ZoiaProjects\ProjectBlog\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
        private UUID $postOrCommentUuid,
        private UUID $userUuid
    ){}

    /**
     * @return UUID
     */
    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @param UUID $uuid
     */
    public function setUuid(UUID $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return UUID
     */
    public function getPostOrCommentUuid(): UUID
    {
        return $this->postOrCommentUuid;
    }

    /**
     * @param UUID $postUuid
     */
    public function setPostUuid(UUID $postOrCommentUuid): void
    {
        $this->postOrCommentUuid = $postOrCommentUuid;
    }

    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    /**
     * @param UUID $userUuid
     */
    public function setUserUuid(UUID $userUuid): void
    {
        $this->userUuid = $userUuid;
    }

}