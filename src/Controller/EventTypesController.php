<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * EventTypes Controller
 *
 * @property \App\Model\Table\EventTypesTable $EventTypes
 */
class EventTypesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $eventTypes = $this->paginate($this->EventTypes);

        $this->set(compact('eventTypes'));
        $this->set('_serialize', ['eventTypes']);
    }

    /**
     * View method
     *
     * @param string|null $id Customer Type id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $eventType = $this->EventTypes->get($id, [
            'contain' => ['Events' => function($q){
                return $q->contain(['Businesses' => function($query){
                    return $query->select(['Businesses.event_id']);
                }]);
            }]
        ]);

        $this->set('eventType', $eventType);
        $this->set('_serialize', ['eventType']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $eventType = $this->EventTypes->newEntity();
        if ($this->request->is('post')) {
            $eventType = $this->EventTypes->patchEntity($eventType, $this->request->getData());
            if ($this->EventTypes->save($eventType)) {
                $this->Flash->success(__('活动类型添加成功.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('活动类型添加失败.请重试.'));
        }
        $this->set(compact('eventType'));
        $this->set('_serialize', ['eventType']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer Type id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $eventType = $this->EventTypes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $eventType = $this->EventTypes->patchEntity($eventType, $this->request->getData());
            if ($this->EventTypes->save($eventType)) {
                $this->Flash->success(__('活动类型修改成功.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('活动类型修改失败.请重试.'));
        }
        $this->set(compact('eventType'));
        $this->set('_serialize', ['eventType']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer Type id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

        $this->request->allowMethod(['post', 'delete']);
        $eventType = $this->EventTypes->get($id);
        $event_count =  $this->EventTypes->Events->find()
            ->where(['event_type_id' => $id])
            ->count();

        if ($event_count) {
            $this->Flash->error(__('该活动类型下有活动，无法删除'));
        } else {
            if ($this->EventTypes->delete($eventType)) {
                $this->Flash->success(__('删除成功'));
            } else {
                $this->Flash->error(__('删除失败. 请重试.'));
            }
        }

        return $this->redirect(['action' => 'index']);
    }
}
