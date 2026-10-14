<?php

namespace App\Http\Controllers;


use App\User;
use App\Simulation;
use App\Message;
use App\ChatUser;
use App\Token;
use Auth;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Events\PrivateMessageSent;
use App\Events\MessageDelivered;
use Illuminate\Support\Facades\Validator;
use Session;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fetchMessages()
    {
        try {
            return Message::with('user')->get();
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function getChats($user)
    {
        try {
            $chats = Message::where('admin_chat_id', $user)->get();
            return $chats;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function privateMessages($user,$simulationId)
    {

        try {
            #finding recieve id for admin and user

            $login_type = auth()->user()->type;

            if ($login_type == 0) {
                // $recieve_id = User::where([['type', 1], ['status', 0]])->value('id');
                $recieve_id = Simulation::where([['id',$simulationId]])->value('created_by');
                $chat_user_identification_id = $user;
                #----------------------update seen status--------------------
                Message::where(['receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id])
                ->update(['seen_status' => 1]);
                #-------------------------------------------------------------
                #----------------------update delivery status--------------------
                Message::where(['receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id])
                    ->update(['delivery_status' => 2]);
            } else {

                $recieve_id = ChatUser::where([['status', 0], ['id', $user]])->value('user_id');
                $admin_chat_id = ChatUser::where([['status', 0], ['id', $user]])->value('id');
                $chat_user_identification_id = ChatUser::where([['status', 0], ['id', $user]])->value('admin_user_id');
                #----------------------update seen status--------------------
                Message::where(['receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id, 'admin_chat_id' => $admin_chat_id])
                ->update(['seen_status' => 1]);
                #--------------------------------------------
                #----------------------update seen status--------------------
                Message::where(['receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id, 'admin_chat_id' => $admin_chat_id])
                ->update(['delivery_status' => 2]);
            }
            $message = $recieve_id;



            #-------------------------------------------------------------


            $privateCommunication = Message::with('user')
                ->where(['user_id' => auth()->id(), 'receiver_id' => $recieve_id, 'chat_user_identification_id' => $chat_user_identification_id])
                ->orWhere(function ($query) use ($user, $recieve_id, $chat_user_identification_id) {
                    $query->where(['user_id' => $recieve_id, 'receiver_id' => auth()->id(), 'chat_user_identification_id' => $chat_user_identification_id]);
                })->get();

            if ($login_type == 0) {
                $data['welcome_status'] = Token::select('welcome_status')->where('user_id',  Auth::user()->id)->first();
                //     dd($data['welcome_status']);
            } else {
                $data['welcome_status'] = Session::get('welcome_status');
                //     dd($data['welcome_status']);
            }



            $data['privateCommunication'] = $privateCommunication;
            $data['user_id'] = $message;

            // broadcast(new MessageDelivered($message))->toOthers();

            return $data;
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            if (request()->has('file')) {
                $filename = request('file')->store('chat');
                $message = Message::create([
                    'user_id' => request()->user()->id,
                    'image' => $filename,
                    'receiver_id' => request('receiver_id')
                ]);
            } else {
                $message = auth()->user()->messages()->create(['message' => $request->message]);
            }

            broadcast(new MessageSent(auth()->user(), $message->load('user')))->toOthers();

            return response(['status' => 'Message sent successfully', 'message' => $message]);
        } catch (Exception $e) {
            report($e);

            return false;
        }
    }

    public function sendPrivateMessage(Request $request, $user,$simulationId)
    {

        try {
            #finding recieve id for admin and user
            $login_type = auth()->user()->type;

            if ($login_type == 0) {
                // $recieve_id = User::where([['type', 1], ['status', 0]])->value('id');
                $recieve_id = Simulation::where([['id',$simulationId]])->value('created_by');
                $chat_user_identification_id = $user;
                $admin_chat_id = ChatUser::where([['status', 0], ['admin_user_id', $user], ['user_id', auth()->user()->id]])->value('id');
            } else {
                $recieve_id = ChatUser::where([['status', 0], ['id', $user]])->value('user_id');
                $chat_user_identification_id = ChatUser::where([['status', 0], ['id', $user]])->value('admin_user_id');
                $admin_chat_id = $user;
            }


            if (request()->has('file')) {

                $filename = request('file')->store('chat');
                //checking file is video or not
                $validator = Validator::make(\Request::all(), [
                    'files' => 'mimes:mp4,mkv'
                ]);

                if ($validator->fails()) {
                    //'receiver_id' => $user->id,
                    $message = Message::create([
                        'user_id' => request()->user()->id,
                        'image' => $filename,
                        'receiver_id' => $recieve_id,
                        'chat_user_identification_id' => $chat_user_identification_id,
                        'admin_chat_id' => $admin_chat_id
                    ]);
                } else {
                    //'image' => $filename,
                    //'receiver_id' => $user->id,
                    $message = Message::create([
                        'user_id' => request()->user()->id,
                        'video' => $filename,
                        'receiver_id' => $recieve_id,
                        'chat_user_identification_id' => $chat_user_identification_id,
                        'admin_chat_id' => $admin_chat_id
                    ]);
                }
            } else {
                $mediatype = $request->type;
                $input['link'] = $request->link;
                $input = $request->all();

                if ($mediatype === "0") {
                    $input['video'] = $request->filepath;
                    $input['type'] = $mediatype;
                }
                if ($mediatype === "1") {
                    $input['image'] = $request->filepath;
                    $input['type'] = $mediatype;
                }
                if ($mediatype === "2") {
                    $input['doc'] = $request->filepath;
                    $input['type'] = $mediatype;
                }
                if ($mediatype == null) {
                    $input['type'] = 3;
                }

                $word = "https:";
                $wordone = "http:";
                $wordtwo = "www.";
                if (strpos($input['message'], $word) !== false) {
                    $msgval = $input['message'];
                    $input['message'] = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
                } elseif (strpos($input['message'], $wordone) !== false) {
                    $msgval = $input['message'];
                    $input['message'] = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
                } elseif (strpos($input['message'], $wordtwo) !== false) {
                    $msgval = $word . $input['message'];
                    $input['message'] = "<a id='linkid-p' target='_blank' :href='$msgval'>" . $msgval . "</a>";
                } else {
                    $input['message'] = $input['message'];
                }

                $input['receiver_id'] = $recieve_id;
                $input['chat_user_identification_id'] = $chat_user_identification_id;
                $input['admin_chat_id'] = $admin_chat_id;

                $message = auth()->user()->messages()->create($input);
                $newCompete = array('name' => 'Others');
            }

            broadcast(new PrivateMessageSent($message->load('user')))->toOthers();

            return response(['status' => 'Message sent successfully', 'message' => $message]);
        } catch (Exception $e) {
            // report($e);
            Log::error($e->getMessage());


            return false;
        }
    }
}
