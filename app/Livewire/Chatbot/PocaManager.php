<?php

namespace App\Livewire\Chatbot;

use Livewire\Component;

class PocaManager extends Component
{
    public $pocaJSON;
    public $pocaData;
    public $showingDeleteConfirmation = false;
    public $selectedItem;
    public $question = [];
    public $answer = [];

    public function loadPOCAData()
    {
        $this->pocaJSON = storage_path('app/chat/knowledge_base.json');
        $this->pocaData = json_decode(file_get_contents($this->pocaJSON), true);

        foreach ($this->pocaData['questions'] as $key => $value) {
            $this->question[$value['id']] = $value['question'];
            $this->answer[$value['id']] = $value['answer'];
        }
    }

    public function showDeleteConfirmation($id)
    {
        $this->selectedItem = $id;
        $this->showingDeleteConfirmation = true;
    }

    public function realignID()
    {
        $updatedJSON = $this->pocaData;
        $ctr = 1;

        foreach ($updatedJSON['questions'] as $key => $value) {
            $updatedJSON['questions'][$key] = [
                'id' => $ctr,
                'question' => $value['question'],
                'answer' => $value['answer'],
                'need-login' => $value['need-login'],
            ];
            $ctr++;
        }

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));
    }

    public function duplicateEntry($id)
    {
        $updatedJSON = $this->pocaData;

        $newQuestions = [];

        foreach ($updatedJSON['questions'] as $key => $value) {
            if ($value['id'] == $id) {
                $newQuestions = [
                    'id' => count($updatedJSON['questions']) + 1,
                    'question' => $value['question'],
                    'answer' => $value['answer'],
                    'need-login' => $value['need-login'],
                ];
            }
        }

        $updatedJSON['questions'][] = $newQuestions;

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));

        $this->loadPOCAData();
    }

    public function editAnswer($id)
    {
        $updatedJSON = $this->pocaData;

        foreach ($updatedJSON['questions'] as $key => $value) {
            if ($value['id'] == $id) {
                $updatedJSON['questions'][$key] = [
                    'id' => $value['id'],
                    'question' => $value['question'],
                    'answer' => $this->answer[$id],
                    'need-login' => $value['need-login'],
                ];
            }
        }

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));

        $this->loadPOCAData();
    }

    public function editQuestion($id)
    {
        $updatedJSON = $this->pocaData;

        foreach ($updatedJSON['questions'] as $key => $value) {
            if ($value['id'] == $id) {
                $updatedJSON['questions'][$key] = [
                    'id' => $value['id'],
                    'question' => $this->question[$id],
                    'answer' => $value['answer'],
                    'need-login' => $value['need-login'],
                ];
            }
        }

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));

        $this->loadPOCAData();
    }

    public function editNeedLogin($id)
    {
        $updatedJSON = $this->pocaData;

        foreach ($updatedJSON['questions'] as $key => $value) {
            if ($value['id'] == $id) {
                if ($value['need-login'] == 'yes') {
                    $needLogin = 'no';
                } else {
                    $needLogin = 'yes';
                }

                $updatedJSON['questions'][$key] = [
                    'id' => $value['id'],
                    'question' => $value['question'],
                    'answer' => $value['answer'],
                    'need-login' => $needLogin,
                ];
            }
        }

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));

        $this->loadPOCAData();
    }

    public function deleteEntry()
    {
        $updatedJSON = $this->pocaData;

        foreach ($updatedJSON['questions'] as $key => $value) {
            if ($value['id'] == $this->selectedItem) {
                unset($updatedJSON['questions'][$key]);
                break;
            }
        }

        $updatedJSON['questions'] = array_values($updatedJSON['questions']);

        file_put_contents($this->pocaJSON, json_encode($updatedJSON, JSON_PRETTY_PRINT));

        $this->dispatch('alert', [
            'message' => 'Entry successfully deleted!',
            'messageType' => 'success',
            'position' => 'top-right',
        ]);
        $this->loadPOCAData();
        $this->realignID();
        $this->reset(['selectedItem']);
        $this->showingDeleteConfirmation = false;
    }

    public function render()
    {
        $this->loadPOCAData();
        $this->realignID();
        return view('livewire.chatbot.poca-manager');
    }
}
