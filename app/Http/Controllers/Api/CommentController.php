<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Video;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ApiResponser;

    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'video_id' => 'required',
        ]);
        $comment = new Comment;

        $comment->comment = $request->comment;

        $comment->user()->associate($request->user());

        $video = Video::find($request->video_id);

        $video->comments()->save($comment);

        return $this->success([], 'comment saved');
    }

    public function replyStore(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'comment_id' => 'required',
            'video_id' => 'required',
        ]);

        $video = Video::find($request->get('video_id'));

        if ($video != null) {
            $comment = Comment::find($request->get('comment_id'));
            if ($comment != null) {
                $reply = new Comment();

                $reply->comment = $request->get('comment');

                $reply->user()->associate($request->user());

                $reply->parent_id = $request->get('comment_id');

                $video->comments()->save($reply);

                return $this->success([], 'reply saved');} else {
                return $this->error('no record found', 404);
            }

        } else {
            return $this->error('no record found', 404);
        }

    }
}
