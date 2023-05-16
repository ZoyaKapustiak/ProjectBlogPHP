<?php

namespace ZoiaProjects\ProjectBlog\Blog;

class LikeComment
{
    public function __construct(
      private UUID $uuid,
      private UUID $commentUuid,
      private UUID $userUuid,
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
    public function getCommentUuid(): UUID
    {
        return $this->commentUuid;
    }

    /**
     * @param UUID $commentUuid
     */
    public function setCommentUuid(UUID $commentUuid): void
    {
        $this->commentUuid = $commentUuid;
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