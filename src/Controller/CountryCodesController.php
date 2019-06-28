<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CountryCodes Controller
 *
 * @property \App\Model\Table\CountryCodesTable $CountryCodes
 */
class CountryCodesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $countryCodes = $this->paginate($this->CountryCodes);

        $this->set(compact('countryCodes'));
        $this->set('_serialize', ['countryCodes']);
    }

    /**
     * View method
     *
     * @param string|null $id Country Code id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $countryCode = $this->CountryCodes->get($id);

        $this->set('countryCode', $countryCode);
        $this->set('_serialize', ['countryCode']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
       $countryCode = $this->CountryCodes->newEntity();
        if ($this->request->is('post')) {
            $countryCode = $this->CountryCodes->patchEntity($countryCode, $this->request->getData());
            if ($this->CountryCodes->save($countryCode)) {
                $this->Flash->success(__('The country code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The country code could not be saved. Please, try again.'));
        }
        $this->set(compact('countryCode'));
        $this->set('_serialize', ['countryCode']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Country Code id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $countryCode = $this->CountryCodes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $countryCode = $this->CountryCodes->patchEntity($countryCode, $this->request->getData());
            if ($this->CountryCodes->save($countryCode)) {
                $this->Flash->success(__('The country code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The country code could not be saved. Please, try again.'));
        }
        $this->set(compact('countryCode'));
        $this->set('_serialize', ['countryCode']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Country Code id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $countryCode = $this->CountryCodes->get($id);
        if ($this->CountryCodes->delete($countryCode)) {
            $this->Flash->success(__('The country code has been deleted.'));
        } else {
            $this->Flash->error(__('The country code could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
