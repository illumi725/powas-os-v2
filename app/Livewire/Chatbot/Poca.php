<?php

namespace App\Livewire\Chatbot;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\Livewire;

class Poca extends Component
{
    public $userInput = '';
    public $messages = [];
    public $isResponseCorrect;
    public $suggestedQuery = [];

    public function sendMessage()
    {
        try {
            if ($this->userInput != '' || $this->userInput != null) {
                $this->messages[] = ['sender' => 'You', 'message' => $this->userInput];

                $this->reset([
                    'isResponseCorrect',
                    'suggestedQuery',
                ]);

                $response = Http::post('http://127.0.0.1:5000/ask', [
                    'question' => $this->userInput,
                ]);

                // $response = Http::post('https://poca.vercel.app//ask', [
                //     'question' => $this->userInput,
                // ]);

                $ratio = 0;
                $bestAnswer = [];

                // dd($response->json());

                foreach ($response->json() as $question => $answer) {
                    if ($answer['ratio'] > $ratio) {
                        $ratio = $answer['ratio'];
                    }

                    $bestAnswer = ['answer' => $answer['answer'], 'need-login' => $answer['need-login']];

                    if ($question > 0) {
                        $this->suggestedQuery[] = $answer['question'];
                    }
                }

                if (count($this->suggestedQuery) > 0) {
                    $this->isResponseCorrect = 'OK';
                }

                if ($bestAnswer['answer'] == '' || $ratio <= 0.85) {
                    $bestAnswer['answer'] = 'Paumanhin pero wala akong makitang sagot para sa iyong katanungan. &#128546;';
                    $bestAnswer['need-login'] = 'no';
                } else {
                    $bestAnswer['answer'] = $response->json()[0]['answer'];
                    $bestAnswer['need-login'] = $response->json()[0]['need-login'];
                }

                $needLogin = $bestAnswer['need-login'];

                if (Auth::user() == null && $needLogin == 'yes') {
                    $this->messages[] = ['sender' => 'POCA', 'message' => 'Ang iyong tanong ay hindi ko masasagot dahil kailangang ikaw muna ay naka-login!'];
                } else {
                    $this->messages[] = ['sender' => 'POCA', 'message' => $bestAnswer['answer']];
                }
            }

            $this->reset([
                'userInput'
            ]);
        } catch (\Throwable $th) {
            $this->reset(['userInput']);
            $this->messages[] = ['sender' => 'POCA', 'message' => 'Something went wrong when I tried pulling your request! Please try again later!'];
        }
    }

    public function sendQuery($query)
    {
        try {
            if ($query != '' || $query != null) {
                $this->messages[] = ['sender' => 'You', 'message' => $query];

                $this->reset([
                    'isResponseCorrect',
                    'suggestedQuery',
                ]);

                $response = Http::post('http://127.0.0.1:5000/ask', [
                    'question' => $query,
                ]);

                // $response = Http::post('https://poca.vercel.app//ask', [
                //     'question' => $this->userInput,
                // ]);

                $ratio = 0;
                $bestAnswer = [];

                // dd($response->json());

                foreach ($response->json() as $question => $answer) {
                    if ($answer['ratio'] > $ratio) {
                        $ratio = $answer['ratio'];
                    }

                    $bestAnswer = ['answer' => $answer['answer'], 'need-login' => $answer['need-login']];

                    if ($question > 0) {
                        $this->suggestedQuery[] = $answer['question'];
                    }
                }

                if (count($this->suggestedQuery) > 0) {
                    $this->isResponseCorrect = 'OK';
                }

                if ($bestAnswer['answer'] == '' || $ratio <= 0.85) {
                    $bestAnswer['answer'] = 'Paumanhin pero wala akong makitang sagot para sa iyong katanungan. &#128546;';
                    $bestAnswer['need-login'] = 'no';
                } else {
                    $bestAnswer['answer'] = $response->json()[0]['answer'];
                    $bestAnswer['need-login'] = $response->json()[0]['need-login'];
                }

                $needLogin = $bestAnswer['need-login'];

                if (Auth::user() == null && $needLogin == 'yes') {
                    $this->messages[] = ['sender' => 'POCA', 'message' => 'Ang iyong tanong ay hindi ko masasagot dahil kailangang ikaw muna ay naka-login!'];
                } else {
                    $this->messages[] = ['sender' => 'POCA', 'message' => $bestAnswer['answer']];
                }
            }

            $this->reset([
                'userInput'
            ]);
        } catch (\Throwable $th) {
            $this->reset(['userInput']);
            $this->messages[] = ['sender' => 'POCA', 'message' => 'Something went wrong when I tried pulling your request! Please try again later!'];
        }
    }

    public function mount()
    {
        $this->messages[] = ['sender' => 'POCA', 'message' => 'Magandang araw! Ako si POCA! Ano ang maipaglilingkod ko!'];
    }

    public function render()
    {
        return view('livewire.chatbot.poca');
    }
}
