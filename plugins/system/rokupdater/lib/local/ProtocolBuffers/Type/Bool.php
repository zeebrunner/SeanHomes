<?php
/**
 * @author Nikolai Kordulla
 */
class ProtocolBuffers_Type_Bool extends ProtocolBuffers_Type_Int
{
	protected $wired_type = ProtocolBuffers_AbstractMessage::WIRED_VARINT;

	/**
	 * Parses the message for this type
	 *
	 * @param array
	 */
	public function ParseFromArray()
	{
		$this->value = $this->reader->next();
		$this->value = ($this->value != 0) ? 1 : 0;
		
		$this->clean();
	}

}