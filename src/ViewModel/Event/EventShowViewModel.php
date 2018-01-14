<?php

namespace App\ViewModel\Event;

/**
 * View model for the view event\show.twig.html
 */
class EventShowViewModel //extends ViewModelBase
{
    /**
     * @var integer
     */
    private $mayEnroll;
    private $event;
    private $enrolledCount;
    private $commitments;
    private $mayMail;
    private $mayInvite;
    private $mayEdit;
    private $mayDelete;
    private $mayDeleteMessage;
    private $mayDownload;
    private $mayCopy;
    private $myDepartmentsAsChief;
    private $myDepartmentsAsDeputy;
    private $deleteForm;
    private $enrollForm;


    /**
     * @return integer
     */
    public function getMayEnroll(){ return $this->mayEnroll; }

          /**
     * @param integer mayEnroll
     * @return self
     */
    public function setMayEnroll($mayEnroll)
    {
        $this->mayEnroll = $mayEnroll;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getEvent(){ return $this->event; }

          /**
     * @param mixed event
     * @return self
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getEnrolledCount(){ return $this->enrolledCount; }

          /**
     * @param mixed enrolledCount
     * @return self
     */
    public function setEnrolledCount($enrolledCount)
    {
        $this->enrolledCount = $enrolledCount;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getCommitments(){ return $this->commitments; }

          /**
     * @param mixed commitments
     * @return self
     */
    public function setCommitments($commitments)
    {
        $this->commitments = $commitments;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMayMail(){ return $this->mayMail; }

          /**
     * @param mixed mayMail
     * @return self
     */
    public function setMayMail($mayMail)
    {
        $this->mayMail = $mayMail;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMayInvite(){ return $this->mayInvite; }

          /**
     * @param mixed mayInvite
     * @return self
     */
    public function setMayInvite($mayInvite)
    {
        $this->mayInvite = $mayInvite;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMayEdit(){ return $this->mayEdit; }

          /**
     * @param mixed mayEdit
     * @return self
     */
    public function setMayEdit($mayEdit)
    {
        $this->mayEdit = $mayEdit;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMayDelete(){ return $this->mayDelete; }

          /**
     * @param mixed mayDelete
     * @return self
     */
    public function setMayDelete($mayDelete)
    {
        $this->mayDelete = $mayDelete;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMayDeleteMessage(){ return $this->mayDeleteMessage; }

          /**
     * @param mixed mayDeleteMessage
     * @return self
     */
    public function setMayDeleteMessage($mayDeleteMessage)
    {
        $this->mayDeleteMessage = $mayDeleteMessage;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMayDownload(){ return $this->mayDownload; }

    /**
     * @param mixed mayDownload
     * @return self
     */
    public function setMayDownload($mayDownload)
    {
        $this->mayDownload = $mayDownload;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMayCopy()
    {
        return $this->mayCopy;
    }

    /**
     * @param mixed mayCopy
     *
     * @return self
     */
    public function setMayCopy($mayCopy)
    {
        $this->mayCopy = $mayCopy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMyDepartmentsAsChief(){ return $this->myDepartmentsAsChief; }

    /**
     * @param mixed myDepartmentsAsChief
     * @return self
     */
    public function setMyDepartmentsAsChief($myDepartmentsAsChief)
    {
        $this->myDepartmentsAsChief = $myDepartmentsAsChief;
        return $this;
    }

          /**
     * @return mixed
     */
    public function getMyDepartmentsAsDeputy(){ return $this->myDepartmentsAsDeputy; }

          /**
     * @param mixed myDepartmentsAsDeputy
     * @return self
     */
    public function setMyDepartmentsAsDeputy($myDepartmentsAsDeputy)
    {
        $this->myDepartmentsAsDeputy = $myDepartmentsAsDeputy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeleteForm(){ return $this->deleteForm; }

    /**
     * @param mixed deleteForm
     * @return self
     */
    public function setDeleteForm($deleteForm)
    {
        $this->deleteForm = $deleteForm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnrollForm(){ return $this->enrollForm; }

    /**
     * @param mixed enrollForm
     * @return self
     */
    public function setEnrollForm($enrollForm)
    {
        $this->enrollForm = $enrollForm;
        return $this;
    }
}
