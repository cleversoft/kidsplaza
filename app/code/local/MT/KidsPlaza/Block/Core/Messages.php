<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Core_Messages extends Mage_Core_Block_Messages{
    /**
     * Retrieve messages in HTML format grouped by type
     *
     * @return string
     */
    public function getGroupedHtml() {
        $types = array(
            Mage_Core_Model_Message::ERROR      => 'alert alert-danger error-msg',
            Mage_Core_Model_Message::WARNING    => 'alert alert-warning warning-msg',
            Mage_Core_Model_Message::NOTICE     => 'alert alert-info notice-msg',
            Mage_Core_Model_Message::SUCCESS    => 'alert alert-success success-msg'
        );
        $html = '';
        foreach ($types as $type => $classes) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages">';
                }
                $html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $classes . '">';
                $html .= '<' . $this->_messagesFirstLevelTagName . '>';

                foreach ( $messages as $message ) {
                    $html.= '<' . $this->_messagesSecondLevelTagName . '>';
                    $html.= '<' . $this->_messagesContentWrapperTagName . '>';
                    $html.= ($this->_escapeMessageFlag) ? $this->escapeHtml($message->getText()) : $message->getText();
                    $html.= '</' . $this->_messagesContentWrapperTagName . '>';
                    $html.= '</' . $this->_messagesSecondLevelTagName . '>';
                }
                $html .= '</' . $this->_messagesFirstLevelTagName . '>';
                $html .= '</' . $this->_messagesSecondLevelTagName . '>';
            }
        }
        if ( $html) {
            $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        }
        return $html;
    }
}