<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Privileges Controller
 *
 * @property \App\Model\Table\PrivilegesTable $Privileges
 */
class PrivilegesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->loadModel('Configs');
        $modules = [
            'Customers' => ['name' => '客户','full_op' => str_split('vaediot')],
            'Businesses' => ['name' => '订单','full_op' => str_split('vaedio')],
            'Events' => ['name' => '活动','full_op' => str_split('vaed')],
            'Users' => ['name' => '员工','full_op' => str_split('vaed')],
            'Departments' => ['name' => '部门','full_op' => str_split('vaed')],
            'Campaigns' => ['name' => '群发','full_op' => str_split('vad')],
            'Statistics' => ['name' => '数据统计','full_op' => str_split('v')],
            'Privileges' => ['name' => '权限','full_op' => str_split('ve')],
            'Configs' => ['name' => '设置','full_op' => str_split('vaed')],
        ];

        $operations = [
            'v' => '查看',
            'a' => '新增',
            'e' => '编辑',
            'd' => '删除',
            'i' => '导入',
            'o' => '导出',
            't' => '转移',
        ];

        
        $roles = $this->loadModel('Roles')->find('list');
        $privileges = json_decode($this->Configs->findByName('privilege')->first()->value,true);
        $this->set(compact('privileges','modules','roles','operations','config_privileges'));
        $this->set('_serialize', ['privileges']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Privilege id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $p = array_filter($this->request->getData('p'));

            $this->loadModel('Configs')->query()
                ->update()
                ->where(['name' => 'privilege'])
                ->set(['value' => json_encode($p)])
                ->execute();
                $this->Flash->success(__('更新成功.'));
                          
        }
        return $this->redirect(['action' => 'index']);
    }
}
