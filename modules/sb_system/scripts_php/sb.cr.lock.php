<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbCR]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class sbCR_Lock {
	
	private $crCurrentSession;
	private $sOwningSessionID;
	private $sSubjectID;
	private $sUserID;
	private $bDeep;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crCurrentSession, $sOwningSessionID, $sSubjectID, $sUserID, $bDeep) {
		$this->crCurrentSession = $crCurrentSession;
		$this->sOwningSessionID = $sOwningSessionID;
		$this->sSubject = $sSubjectID;
		$this->sUserID = $sUserID;
		$this->bDeep = $bDeep;
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the user ID of the user who owns this lock. This is the value of 
	* the jcr:lockOwner property of the lock-holding node. It is also the value 
	* returned by Session.getUserID at the time that the lock was placed. The 
	* lock owner's identity is only provided for informational purposes. It does 
	* not govern who can perform an unlock or make changes to the locked nodes; 
	* that depends entirely upon who the token holder is.
	* @return 
	*/
	public function getLockOwner() {
		return ($this->sUserID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* May return the lock token for this lock. If this lock is open-scoped and 
	* the current session holds the lock token for this lock, then this method 
	* will return that lock token. Otherwise this method will return null.
	* @return 
	*/
	public function getLockToken() {
		return ($this->sUserID.':'.$this->sSubjectID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns the lock holding node. Note that N.getLock().getNode()  (where N 
	* is a locked node) will only return N  if N is the lock holder. If N is in 
	* the subtree of the lock holder, H, then this call will return H.
	* @param 
	* @return 
	*/
	public function getNode() {
		$nodeSubject = $this->crCurrentSession->getNodeByIdentifier($this->sSubjectID);
		return ($nodeSubject);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this is a deep lock; false otherwise.
	* @return 
	*/
	public function isDeep() {
		return ($this->bDeep);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this is a session-scoped lock and the scope is bound to 
	* the current session. Returns false if this is an open-scoped lock or is 
	* session-scoped but the scope is bound to another session.
	* @return 
	*/
	public function isSessionScoped() {
		// TODO: implement session-scoped locks?
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if the current session is the owner of this lock, either 
	* because it is session-scoped and bound to this session or open-scoped and 
	* this session currently holds the token for this lock. Returns false 
	* otherwise.
	* @return 
	*/
	public function isLockOwningSession() {
		if ($this->crCurrentSession->getFingerprint() == $this->sOwningSessionID) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns true if this Lock object represents a lock that is currently in 
	* effect. If this lock has been unlocked either explicitly or due to an 
	* implementation-specific limitation (like a timeout) then it returns false. 
	* Note that this method is intended for those cases where one is holding a 
	* Lock Java object and wants to find out whether the lock (the JCR-level 
	* entity that is attached to the lockable node) that this object originally 
	* represented still exists. For example, a timeout or explicit unlock will
	* remove a lock from a node but the Lock  Java object corresponding to that 
	* lock may still exist, and in that case its isLive method will return 
	* false.
	* @param 
	* @return 
	*/
	public function isLive() {
		throw new LazyBastardException();
	}
	
	//--------------------------------------------------------------------------
	/**
	* If this lock's time-to-live is governed by a timer, this method resets 
	* that timer so that the lock does not timeout and expire. If this lock's 
	* time-to-live is not governed by a timer, then this method has no effect.
	* A LockException is thrown if this Session does not hold the correct lock 
	* token for this lock. 
	* @param 
	* @return 
	*/
	public function refresh() {
		// TODO: what to do about session scoped locking? session ends with request!
		/*$stmtRefreshLocks = $this->crSession->prepareKnown('sbCR/node/locking/refreshLock');
		$stmtRefreshLocks->bindParam('node_uuid', $this->sSubjectID, PDO::PARAM_STR);
		$stmtRefreshLocks->execute();
		$stmtRefreshLocks->closeCursor();*/
	}
	
}

?>