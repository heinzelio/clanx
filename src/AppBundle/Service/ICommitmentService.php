<?php
namespace App\Service;


use App\Entity\Commitment;
use App\Entity\Event;
use App\ViewModel\Commitment\CommitmentViewModel;

interface ICommitmentService
{
    /**
     * Gets a collection of commitments of the currently
     * logged in user for the given event.
     * @param  Event  $event
     * @return Commitment[]
     */
    public function getCurrentUsersCommitmentsFor(Event $event);

    /**
     * Saves the commitment for the logged in user and the
     * given event.
     * @param  Event $event
     * @param  CommitmentViewModel $vm
     * @return Commitment Returns null, if the operation failed.
     */
    public function saveCommitment(Event $event, CommitmentViewModel $vm);

    /**
     * updates the commitment and its answers
     * @param  CommitmentViewModel $vm
     * @param  Commitment          $commitment
     * @return boolean true if update succeeded.
     */
    public function updateCommitment(CommitmentViewModel $vm, Commitment $commitment);

    /**
     * deletes the given commitment. Does nothing, if commitment is not definied.
     * @param  Commitment $commitment
     * @return boolean                 true if successful
     */
    public function deleteCommitment(Commitment $commitment);
}

?>
