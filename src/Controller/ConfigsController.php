<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Configs Controller
 *
 * @property \App\Model\Table\ConfigsTable $Configs
 */
class ConfigsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $state = $this->Configs->findByName('state')->first()->value;
        $source = $this->Configs->findByName('source')->first()->value;
        $ahead = $this->Configs->findByName('ahead')->first()->value;
        $colorArr = ['#DB2828','#F2711C','#FBBD08','#B5CC18','#21BA45','#00B5AD','#2185D0','#6435C9','#A333C8','#E03997','#A5673F','#767676','#1B1C1D'];
        $this->set(compact('state','source','ahead'));
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function edit()
    {
        
        if ($this->request->is('post')) {
            $state = $this->request->getData('state');            
            $config = $this->Configs->findByName('state')->first();            
            $config->value = $state;
            $this->Configs->save($config);

            $source = $this->request->getData('source');            
            $config = $this->Configs->findByName('source')->first();            
            $config->value = $source;
            $this->Configs->save($config);

            $ahead = $this->request->getData('ahead');            
            $config = $this->Configs->findByName('ahead')->first();            
            $config->value = $ahead;
            $this->Configs->save($config);
        }
        $this->Flash->success(__('保存成功'));
        return $this->redirect(['action' => 'index']);
    }
}
