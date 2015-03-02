<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Operation\MailChimpBundle\Entity;

use CampaignChain\CoreBundle\Entity\Meta;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="campaignchain_operation_mailchimp_newsletter")
 */
class MailChimpNewsletter extends Meta
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="CampaignChain\CoreBundle\Entity\Operation", cascade={"persist"})
     */
    protected $operation;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $campaignId;

    /**
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    protected $webId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    protected $listId;

    /**
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    protected $folderId;

    /**
     * @ORM\Column(type="integer", nullable=false, unique=true)
     */
    protected $templateId;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    protected $contentType;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $sendTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $contentUpdatedTime;

    /**
     * @ORM\Column(type="string", length=8, nullable=false)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $fromName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $fromEmail;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $subject;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $archiveUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $archiveUrlLong;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $trackingHtmlClicks;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $trackingTextClicks;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $trackingOpens;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set operation
     *
     * @param \CampaignChain\CoreBundle\Entity\Operation $operation
     * @return Status
     */
    public function setOperation(\CampaignChain\CoreBundle\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \CampaignChain\CoreBundle\Entity\Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param mixed $archiveUrl
     */
    public function setArchiveUrl($archiveUrl)
    {
        $this->archiveUrl = $archiveUrl;
    }

    /**
     * @return mixed
     */
    public function getArchiveUrl()
    {
        return $this->archiveUrl;
    }

    /**
     * @param mixed $archiveUrlLong
     */
    public function setArchiveUrlLong($archiveUrlLong)
    {
        $this->archiveUrlLong = $archiveUrlLong;
    }

    /**
     * @return mixed
     */
    public function getArchiveUrlLong()
    {
        return $this->archiveUrlLong;
    }

    /**
     * @param mixed $campaignId
     */
    public function setCampaignId($campaignId)
    {
        $this->campaignId = $campaignId;
    }

    /**
     * @return mixed
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $contentUpdatedTime
     */
    public function setContentUpdatedTime($contentUpdatedTime)
    {
        $this->contentUpdatedTime = $contentUpdatedTime;
    }

    /**
     * @return mixed
     */
    public function getContentUpdatedTime()
    {
        return $this->contentUpdatedTime;
    }

    /**
     * @param mixed $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }

    /**
     * @return mixed
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * @param mixed $folderId
     */
    public function setFolderId($folderId)
    {
        $this->folderId = $folderId;
    }

    /**
     * @return mixed
     */
    public function getFolderId()
    {
        return $this->folderId;
    }

    /**
     * @param mixed $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }

    /**
     * @return mixed
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param mixed $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return mixed
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param mixed $listId
     */
    public function setListId($listId)
    {
        $this->listId = $listId;
    }

    /**
     * @return mixed
     */
    public function getListId()
    {
        return $this->listId;
    }

    /**
     * @param mixed $sendTime
     */
    public function setSendTime($sendTime)
    {
        $this->sendTime = $sendTime;
    }

    /**
     * @return mixed
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $trackingHtmlClicks
     */
    public function setTrackingHtmlClicks($trackingHtmlClicks)
    {
        $this->trackingHtmlClicks = $trackingHtmlClicks;
    }

    /**
     * @return mixed
     */
    public function getTrackingHtmlClicks()
    {
        return $this->trackingHtmlClicks;
    }

    /**
     * @param mixed $trackingOpens
     */
    public function setTrackingOpens($trackingOpens)
    {
        $this->trackingOpens = $trackingOpens;
    }

    /**
     * @return mixed
     */
    public function getTrackingOpens()
    {
        return $this->trackingOpens;
    }

    /**
     * @param mixed $trackingTextClicks
     */
    public function setTrackingTextClicks($trackingTextClicks)
    {
        $this->trackingTextClicks = $trackingTextClicks;
    }

    /**
     * @return mixed
     */
    public function getTrackingTextClicks()
    {
        return $this->trackingTextClicks;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $webId
     */
    public function setWebId($webId)
    {
        $this->webId = $webId;
    }

    /**
     * @return mixed
     */
    public function getWebId()
    {
        return $this->webId;
    }
}
