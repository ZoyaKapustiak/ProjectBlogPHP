<?php

namespace ZoiaProjects\ProjectBlog\Blog;

class Like
{
    public function __construct(
        private UUID $uuid,
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
     * @param UUID $userUuid
     */
    public function setUserUuid(UUID $userUuid): void
    {
        $this->userUuid = $userUuid;
    }


    /**
     * @return UUID
     */
    public function getUserUuid(): UUID
    {
        return $this->userUuid;
    }

    /**
     * @param UUID $user
     */
    public function setUser(UUID $user): void
    {
        $this->user = $user;
    }

}