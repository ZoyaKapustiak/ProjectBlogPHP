<?php

namespace ZoiaProjects\ProjectBlog\Blog;


class LikeComment extends Like
{
    private UUID $commentUuid;
    public function __construct(
        UUID $uuid,
        UUID $userUuid,
        UUID $commentUuid
    ){
        parent::__construct($uuid, $userUuid);
        $this->commentUuid = $commentUuid;
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
    public function setPostUuid(UUID $commentUuid): void
    {
        $this->commentUuid = $commentUuid;
    }


}