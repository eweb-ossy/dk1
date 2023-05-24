<?php 

class MY_Model extends CI_Model
{
  
  protected $_table;
  
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $clazz = get_class($this);
    $this->_table = strtolower(substr($clazz, strpos($clazz, '_') + 1));
  }
  
  public function find($select = '*', $where = [], $orderby = 'id')
  {
    return $this->db->select($select)->where($where)->order_by($orderby)->get($this->_table)->result();
  }
  
  public function find_row($select = '*', $where = [])
  {
    return $this->db->select($select)->where($where)->get($this->_table)->row();
  }
  
  public function find_join($select = '*', $join_table, $join_column, $join_type = 'inner', $where = [])
  {
    return $this->db->select($select)->join($join_table, $join_column, $join_type)->where($where)->get($this->_table)->result();
  }
  
  public function insert()
  {
    $now = $this->now();
    $this->db->set(['created_at'=>$now, 'updated_at'=>$now]);
    $ret = $this->db->insert($this->_table, $this);
    if ($ret === FALSE) {
      return FALSE;
    }
    return $this->db->insert_id();
  }
  
  public function update($id, $data = null)
  {
    if ($data === null) {
      $data = $this;
    }
    $data['updated_at'] = $this->now();
    $ret = $this->db->update($this->_table, $data, ['id'=>$id]);
    if ($ret === FALSE) {
      return FALSE;
    }
  }
  
  public function delete($id)
  {
    $ret = $this->db->delete($this->_table, ['id'=>$id]);
    if ($ret === FALSE) {
      return FALSE;
    }
  }
  
  public function now()
  {
    return date('Y-m-d H:i:s');
  }
}