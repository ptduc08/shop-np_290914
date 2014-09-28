<?php
class Admin_Model_Contact extends Zend_Db_Table {
	protected $_name = 'contacts';
	protected $_primary = 'id';
	
	public function changeStatus($arrParam = null, $options = null) {
		//----- truong hop thay doi thuoc tinh status
		if ($options['task'] == 'admin-newsarticle-change-status') {
			$cid = $arrParam['cid'];
			if (count($cid) > 0) {				//----- change status cho nhieu doi tuong
				switch ($arrParam['type']) {
					case 'active':
						$status = 1;
						break;
					case 'inactive':
						$status = 0;
						break;
				}
				$strId = implode(',', $cid);
				$data = array('status'=>$status);
				$where = 'id IN (' .$strId .')';
				$this->update($data, $where);
			}
			if (isset($arrParam['id'])) {		//----- change status cho mot doi tuong
				$id = $arrParam['id'];
				switch ($arrParam['type']) {
					case 'active':
						$status = 1;
						break;
					case 'inactive':
						$status = 0;
						break;
				}
				$data = array('status'=>$status);
				$where = 'id = ' .$id;
				$this->update($data, $where);
			}
		}
		
	}
	
	public function changeLockStatus($arrParam = null, $options = null) {
		//----- truong hop thay doi thuoc tinh status
		if ($options['task'] == 'admin-contact-change-lock-status') {
			$cid = $arrParam['cid'];
			if (count($cid) > 0) {				//----- change lock status cho nhieu doi tuong
				switch ($arrParam['type']) {
					case 'active':
						$lock_status = 1;
						break;
					case 'inactive':
						$lock_status = 0;
						break;
				}
				$strId = implode(',', $cid);
				$data = array('lock_status'=>$lock_status);
				$where = 'id IN (' .$strId .')';
				$this->update($data, $where);
			}
			if (isset($arrParam['id'])) {		//----- change status cho mot doi tuong
				$id = $arrParam['id'];
				switch ($arrParam['type']) {
					case 'active':
						$lock_status = 1;
						break;
					case 'inactive':
						$lock_status = 0;
						break;
				}
				$data = array('lock_status'=>$lock_status);
				$where = 'id = ' .$id;
				$this->update($data, $where);
			}
		}
	
	}
	
	public function countItem($arrParam = null, $options = null) {
		$db = Zend_Registry::get('connectDb');
		$ssFilter = $arrParam['ssFilter'];
		$select = $db->select()
					->from('gallery AS g',array('COUNT(g.id) AS total'));
		
		//----- neu co tu khoa tim kiem thi bo sung them where cho select
		if (!empty($ssFilter['keywords'])) {
			$keywords = '%' .$ssFilter['keywords'] . '%';
			$select->where('g.gallery_name LIKE ?',$keywords);
			
		}
		
		$result = $db->fetchOne($select);
		return $result;
	}
	
	public function deleteItem($arrParam = null, $options = null) {
		//----- truong hop xoa mot doi tuong -------------------------------------------------------------
		if ($options['task'] == 'admin-contact-delete') {
			$where = ' id = ' .$arrParam['id'];
			$this->delete($where);
			
		}
		
		//----- truong hop xoa nhieu doi tuong -------------------------------------------------------------
		if ($options['task'] == 'admin-contact-multi-delete') {
			$cid = $arrParam['cid'];
			if (count($cid) > 0) {
				foreach ($cid as $key=>$val) {
					//----- truyen id cua gallery bi xoa vao mang $arrParam
					$arrParam['id'] = $val;
					$where = ' id = ' .$arrParam['id'];
					$this->delete($where);
				}

			}
		}
	}
	
	public function getItem($arrParam = null, $options = null) {
		$db = Zend_Registry::get('connectDb');
		//$db = Zend_Db::factory($adapter,$config);
		//----- lay thong tin cua mot contact khi biet id cua contact -------------------------------------------
		if ($options['task'] == 'admin-contact-info') {
			$select = $db->select()
						 ->from('contacts AS c')
						 ->where('c.id = ?',$arrParam['id'],INTEGER);
			
			$result = $db->fetchRow($select);
		}
		
		//----- lay ten cover_image cua gallery dang bi xoa de xoa cover_image -----------------------------------
		if ($options['task'] == 'admin-get-deleting-gallery-coverimage') {
			$db = Zend_Registry::get('connectDb');
			$select = $db->select()
						 ->from('gallery AS g',array('g.cover_image'))
						 ->where('g.id = ?',$arrParam['id'],INTEGER);
			$result = $db->fetchOne($select);
		}
		
		//----- lay order lon nhat trong bang gallery
		if ($options['task'] == 'admin-get-biggest-gallery-order') {
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			$select = $db->select()
						 ->from('gallery AS g','g.order')
						 ->order('g.order DESC');
			$result = $db->fetchOne($select);
		}
		
		//----- lay lock_status cua contact duoc chon ------------------------------------------------------------
		if ($options['task'] == 'admin-contact-get-lock-status') {
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			if (isset($arrParam['cid']) && count($arrParam['cid'] > 0)) {
				//----- truong hop lay lock_status cua nhieu doi tuong
				$arrLockStatus = array();
				$arrCid = $arrParam['cid'];
				$select = $db->select()
							 ->from('contacts AS c','c.lock_status')
							 ->where('c.id IN (?)',$arrCid);
				$result = $db->fetchAll($select);
		
			} else if (isset($arrParam['id'])) {
				//----- truong hop lay lock_status cua mot doi tuong
				$select = $db->select()
							 ->from('contacts AS c','c.lock_status')
							 ->where('c.id = ?',$arrParam['id'],INTEGER);
				$result = $db->fetchOne($select);
			}
				
		}
		
		//----- lay order lon nhat trong bang service
		if ($options['task'] == 'admin-get-biggest-product-order') {
			$db = Zend_Registry::get('connectDb');
			//$db = Zend_Db::factory($adapter,$config);
			$select = $db->select()
						 ->from('products AS p','p.order')
						 ->order('p.order DESC');
			$result = $db->fetchOne($select);
		}
		
		return $result;
	}
	
	public function listItem($arrParam = null, $options = null) {
		$db = Zend_Registry::get('connectDb');
		
		if ($options['task'] == 'admin-contact-list') {
			$paginator = $arrParam['paginator'];
			$ssFilter = $arrParam['ssFilter'];
			//$db = Zend_Db::factory($adapter,$config);
			$select = $db->select()
						 ->from('contacts AS c');

			//----- neu co thong so sap xep thi bo sung order de sap xep
			if (!empty($ssFilter['col']) && !empty($ssFilter['col'])) {
				$select->order($ssFilter['col'] .' ' . $ssFilter['order']);
			}
			
			//----- neu co thong so phan trang thi bo sung limitPage cho select
			if ($paginator['itemCountPerPage'] > 0) {
				$currentPage = $paginator['currentPage'];
				$itemCountPerPage = $paginator['itemCountPerPage'];
				$select->limitPage($currentPage, $itemCountPerPage);
			}
			
			//----- neu co tu khoa tim kiem thi bo sung where cho select
			if (!empty($ssFilter['keywords'])) {
				$keywords = '%' .$ssFilter['keywords'] . '%';
				$select->where('c.contact_name LIKE ?',$keywords);
			}
			
			$result = $db->fetchAll($select);
			
		}
		
		//----- lay danh sach cac contact co id nam trong mot mang (mang $arrParam['cid'])
		if ($options['task'] == 'admin-contact-list-from-array') {
			$cid = $arrParam['cid'];
			$select = $db->select()
						 ->from('contacts AS c')
						 ->where('c.id IN (?)',$cid);
			$result = $db->fetchAll($select);
		}
		
		//----- lay id va gallery_name cua tat ca cac gallery album (phuc vu cho selectbox)
		if ($options['task'] == 'admin-gallery-list-selectbox') {
			$select = $db->select()
						 ->from('gallery',array('id','gallery_name'));
			$result = $db->fetchPairs($select);
			$result[0] = '-- Select a Gallery --';
			//----- sap xep lai mang $result theo khoa
			ksort($result);
		}
		
		//----- lay id va category_name cua cac nhom tin tuc, tru nhom tin tuc hien tai (phuc vu cho selectbox)
		if ($options['task'] == 'admin-newscategory-list-selectbox-parent-category') {
			//----- lay id cua nhom tin tuc dang duoc chon
			if (isset($arrParam['id'])) {
				$current_category_id = $arrParam['id'];
			} else {
				$current_category_id = 0;
			}
			
			$select = $db->select()
						 ->from('news_category',array('id','category_name'))
						 ->where('id != ?',$current_category_id,INTEGER);
			$result = $db->fetchPairs($select);
			$result[0] = '-- Select a Category --';
			//----- sap xep lai mang $result theo khoa
			ksort($result);
		}
		
		//----- lay danh sach cac category cua mot tin tuc, khi biet id cua tin tuc do
		if ($options['task'] == 'admin-newsarticle-list-category') {
			$article_id = $arrParam['article_id'];
			$select = $db->select()
						 ->from('news_category_article AS nca','nca.category_id')
						 ->joinLeft('news_category AS nc', 'nc.id = nca.category_id','nc.category_name')
						 ->where('nca.article_id = ?', $article_id, INTEGER);
			$result = $db->fetchAll($select);
		}
		
		//----- lay danh sach cac category ID cua mot tin tuc, khi biet id cua tin tuc do
		if ($options['task'] == 'admin-newsarticle-list-category-only-id') {
			$article_id = $arrParam['article_id'];
			//$db = Zend_Db::factory($adapter,$config);
			$select = $db->select()
						 ->from('news_category_article AS nca','nca.category_id')
						 ->where('nca.article_id = ?', $article_id, INTEGER);
			$tmp = $db->fetchAll($select);
			$result = array();
			if (count($tmp) > 0) {
				foreach ($tmp as $key=>$val) {
					$result[] = $val['category_id'];
				}
			}
		}
		
		return $result;
	}
	
	public function publish($arrParam = null, $options = null) {
		if (isset($arrParam['cid'])) {
			$cid = $arrParam['cid'];
		} else {
			$cid = array();
		}
	
		if (count($cid) > 0) {				//----- publish cho nhieu doi tuong
			switch ($arrParam['type']) {
				case 'active':
					$publish = 1;
					break;
				case 'inactive':
					$publish = 0;
					break;
			}
			$strId = implode(',', $cid);
			$data = array('publish'=>$publish);
			$where = 'id IN (' .$strId .')';
			$this->update($data, $where);
				
			$this->saveItem($arrParam,array('task'=>'admin-contact-publish-multi-contact'));
		}
		if (isset($arrParam['id'])) {		//----- publish cho mot doi tuong
			$id = $arrParam['id'];
			switch ($arrParam['type']) {
				case 'active':
					$publish = 1;
					break;
				case 'inactive':
					$publish = 0;
					break;
			}
			$data = array('publish'=>$publish);
			$where = 'id = ' .$id;
			$this->update($data, $where);
				
			$this->saveItem($arrParam,array('task'=>'admin-contact-publish-one-contact'));
		}
	
	}
	
	public function saveItem($arrParam = null, $options = null) {
		if ($options['task'] == 'admin-gallery-add') {
			$row 				= $this->fetchNew();
			
			$row->gallery_name	= $arrParam['gallery_name'];
			$row->cover_image	= $arrParam['cover_image'];
			$row->publish	 	= $arrParam['publish'];
			$row->order		 	= $arrParam['order'];
			$row->lock_status 	= $arrParam['lock_status'];
			
			//----- lay user_id cua user tao ra nhom nay
			$info = new Zendvn_System_Info();
			$user_id = $info->getMemberInfo('id');
			$row->created_id 	= $user_id;
			$row->created_time	= date("Y-m-d H:m:s");
			
			$row->last_modified_id = 0;
			$row->last_modified_time = '0000-00-00 00:00:00';
			
			if ($arrParam['publish'] == 0) {
				//----- nguoi soan bai khong publish bai viet, hoac khong co quyen publish bai viet
				$row->publisher_id	= 0;
				$row->publish_time	= '0000-00-00 00:00:00';
			} else {
				//----- nguoi soan bai publish luon bai viet thi cap nhat luon thong tin nguoi va thoi gian publish
				$row->publisher_id	= $user_id;;
				$row->publish_time	= date("Y-m-d H:m:s");
			}
			
			$row->save();
			
		}
		
		if ($options['task'] == 'admin-gallery-edit') {
			$where 				= ' id = ' . $arrParam['id'];
			$row 				= $this->fetchRow($where);
			
			$row->gallery_name	= $arrParam['gallery_name'];
			$row->description	= $arrParam['description'];
			$row->cover_image	= $arrParam['cover_image'];
			$row->publish	 	= $arrParam['publish'];
			$row->order		 	= $arrParam['order'];
			$row->lock_status 	= $arrParam['lock_status'];
			
			//----- lay user_id cua user dang login vao he thong
			$info = new Zendvn_System_Info();
			$user_id = $info->getMemberInfo('id');
			$row->last_modified_id 	= $user_id;
			$row->last_modified_time = date("Y-m-d H:m:s");

			$row->save();

		}
		
		if ($options['task'] == 'admin-contact-publish-one-contact') {
			$where 				= ' id = ' . $arrParam['id'];
			$row 				= $this->fetchRow($where);
			//----- lay user_id cua user dang login vao he thong
			$info = new Zendvn_System_Info();
			$user_id = $info->getMemberInfo('id');
			$row->publisher_id 	= $user_id;
			$row->publish_time = date("Y-m-d H:m:s");
			$row->save();
		}
		
		if ($options['task'] == 'admin-contact-publish-multi-contact') {
			$cid = $arrParam['cid'];
			if (count($cid) > 0) {
				foreach($cid as $key=>$val) {
					$contact_id 		= $val;
					$where 				= ' id = ' . $contact_id;
					$row 				= $this->fetchRow($where);
					//----- lay user_id cua user dang login vao he thong
					$info = new Zendvn_System_Info();
					$user_id = $info->getMemberInfo('id');
					$row->publisher_id 	= $user_id;
					$row->publish_time = date("Y-m-d H:m:s");
					$row->save();
				}
			} 
		}
	}
	
	public function sortItem($arrParam = null, $options = null) {
		/* if (isset($arrParam['cid'])) {
			$cid = $arrParam['cid'];
		} else {
			$cid = array();
		} */
		
		$order = $arrParam['order'];
		if (count($order) > 0) {
			foreach ($order as $key=>$value) {
				$where = 'id = ' .$key;
				$data = array('order'=>$value);
				$this->update($data,$where);
			}
		}
		
	}
}