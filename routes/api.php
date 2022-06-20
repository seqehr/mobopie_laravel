<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikesController;
use App\Http\Controllers\StoriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostTagsController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('book', [BookController::class, 'index']);
Route::post('account/login', [AuthController::class, 'requestToken']);
Route::get('account/notlogin', [AuthController::class, 'NotLogin'])->name('login');
Route::post('account/sendemail', [AuthController::class, 'SendVEmail']);
Route::post('account/checkemail', [AuthController::class, 'CheckEmail']);
Route::post('account/checkname', [AuthController::class, 'CheckUsername']);
Route::post('account/register', [AuthController::class, 'Register']);
Route::post('account/verify', [AuthController::class, 'VerifyCode']);
Route::post('account/forget', [AuthController::class, 'ForgetPass']);
Route::post('account/changepass', [AuthController::class, 'ChangePass'])->middleware('auth:sanctum');
Route::post('account/updateprofile', [ProfileController::class, 'CompeleteProfile'])->middleware('auth:sanctum');
Route::post('post/create', [PostController::class, 'CreatePost'])->middleware('auth:sanctum');
Route::post('comment/create', [CommentsController::class, 'CreateComment'])->middleware('auth:sanctum');
Route::post('like/create', [LikesController::class, 'CreateLike'])->middleware('auth:sanctum');
Route::post('account/profile', [ProfileController::class, 'UserProfile'])->middleware('auth:sanctum');
Route::post('userpost/posts', [PostController::class, 'UserPosts'])->middleware('auth:sanctum');
Route::get('post/singlepost/{id}', [PostController::class, 'SinglePost'])->middleware('auth:sanctum');

Route::post('story/create', [StoriesController::class, 'CreateStory'])->middleware('auth:sanctum');
Route::post('story/userstories', [StoriesController::class, 'UserStories'])->middleware('auth:sanctum');
Route::post('story/otheruserstories', [StoriesController::class, 'OtherUserStories'])->middleware('auth:sanctum');
Route::post('story/storyviews', [StoriesController::class, 'StoryViews'])->middleware('auth:sanctum');

Route::post('post/tags', [PostTagsController::class, 'PostTags'])->middleware('auth:sanctum');
// Route::post('post/search', [PostTagsController::class, 'SearchTags'])->middleware('auth:sanctum');
Route::post('tags/search', [PostTagsController::class, 'SearchTags'])->middleware('auth:sanctum');
Route::post('post/search/bytitle', [PostController::class, 'SearchPostByTitle'])->middleware('auth:sanctum');
Route::post('post/search/bytitle', [PostController::class, 'SearchPostByTitle'])->middleware('auth:sanctum');

Route::post('tags/searchonlytags', [PostTagsController::class, 'SearchOnlyTags'])->middleware('auth:sanctum');

Route::post('user/follow', [App\Http\Controllers\FollowersController::class, 'FollowUser'])->middleware('auth:sanctum');
Route::post('user/followinglist', [App\Http\Controllers\FollowersController::class, 'UserFollowing'])->middleware('auth:sanctum');

Route::post('user/usersearch', [UserController::class, 'UserSearch'])->middleware('auth:sanctum');
Route::post('maps/mapsearch', [AuthController::class, 'SearchMaps'])->middleware('auth:sanctum');
// Route::get('user/followerlist', [UserController::class, 'UserFollowers'])->middleware('auth:sanctum');
Route::post('posts/followingposts', [PostController::class, 'FollowingPosts'])->middleware('auth:sanctum');
Route::post('follow/followercounter', [App\Http\Controllers\FollowersController::class, 'FollowerCounter'])->middleware('auth:sanctum');
Route::post('follow/followingcounter', [App\Http\Controllers\FollowersController::class, 'FollowingCounter'])->middleware('auth:sanctum');
// Route::post('follow/pendings', [App\Http\Controllers\FollowersController::class, 'Pendings'])->middleware('auth:sanctum');
Route::post('user/followerslist', [App\Http\Controllers\FollowersController::class, 'UserFollowers'])->middleware('auth:sanctum');
Route::post('user/followerslist', [App\Http\Controllers\FollowersController::class, 'UserFollowers'])->middleware('auth:sanctum');
Route::post('user/unfollow', [App\Http\Controllers\FollowersController::class, 'UnfollowUsers'])->middleware('auth:sanctum');
Route::post('user/regionsearch', [App\Http\Controllers\ProfileController::class, 'RegionSearch'])->middleware('auth:sanctum');
Route::post('chat/newpvm', [App\Http\Controllers\MessagesController::class, 'SendPrivateMessage'])->middleware(['auth:sanctum', 'seen']);
Route::post('message', [App\Http\Controllers\Controller::class, 'SendPush'])->middleware('auth:sanctum');
Route::post('chat/usermessages', [App\Http\Controllers\MessagesController::class, 'UserMessages'])->middleware('auth:sanctum');
Route::post('chat/userchats', [App\Http\Controllers\MessagesController::class, 'UserChats'])->middleware('auth:sanctum');
Route::post('user/profiles', [App\Http\Controllers\PostController::class, 'OtherUserProfiles'])->middleware('auth:sanctum');
Route::post('chat/seenmessage', [App\Http\Controllers\MessagesController::class, 'SeenMessage'])->middleware('auth:sanctum');
Route::post('posts/explore', [App\Http\Controllers\PostController::class, 'Explore'])->middleware('auth:sanctum');
Route::post('maps/nearby', [App\Http\Controllers\UserController::class, 'NearBy'])->middleware('auth:sanctum');
Route::post('posts/delete', [App\Http\Controllers\PostController::class, 'DeletePost'])->middleware('auth:sanctum');
Route::post('stories/delete', [App\Http\Controllers\StoriesController::class, 'DeleteStory'])->middleware('auth:sanctum');
Route::post('reports/create', [App\Http\Controllers\ReportUsersController::class, 'CreateReport'])->middleware('auth:sanctum'); // nreed more edit
Route::post('chats/deletechat', [App\Http\Controllers\MessagesController::class, 'DeleteChat'])->middleware('auth:sanctum');
Route::post('chats/deletemessage', [App\Http\Controllers\MessagesController::class, 'DeleteMessage'])->middleware('auth:sanctum');
Route::post('intrests/create', [App\Http\Controllers\IntrestedController::class, 'CreateIntrest'])->middleware('auth:sanctum');
Route::post('intrests/userintrests', [App\Http\Controllers\IntrestedController::class, 'Userintrests'])->middleware('auth:sanctum');
Route::post('intrests/tags', [App\Http\Controllers\IntrestedController::class, 'IntrestTags'])->middleware('auth:sanctum');
Route::post('intrests/cats', [App\Http\Controllers\IntrestedController::class, 'IntrestCats'])->middleware('auth:sanctum');
Route::post('maps/updateloc', [App\Http\Controllers\UserController::class, 'Updateloc'])->middleware('auth:sanctum');
Route::post('vitrin/create', [App\Http\Controllers\ProfileController::class, 'createVitrinPhotos'])->middleware('auth:sanctum');
Route::post('intrests/createlike', [App\Http\Controllers\IntrestedController::class, 'CreateLike'])->middleware('auth:sanctum');
Route::post('intrests/likedpeople', [App\Http\Controllers\IntrestedController::class, 'LikedPeople'])->middleware('auth:sanctum');
Route::post('vitrin/uservitrin', [App\Http\Controllers\ProfileController::class, 'UserVitrin'])->middleware('auth:sanctum');
Route::post('fcm/create', [App\Http\Controllers\UserController::class, 'CreateFcm'])->middleware('auth:sanctum');
Route::post('block/create', [App\Http\Controllers\BlockUserController::class, 'Create'])->middleware('auth:sanctum');
Route::post('block/list', [App\Http\Controllers\BlockUserController::class, 'BlockList'])->middleware('auth:sanctum');
Route::post('account/createlanguage', [App\Http\Controllers\ProfileController::class, 'CreateLanguage'])->middleware('auth:sanctum');
Route::post('savepost/create', [App\Http\Controllers\PostController::class, 'CreateSavedPost'])->middleware('auth:sanctum');
Route::post('savepost/list', [App\Http\Controllers\PostController::class, 'SavedList'])->middleware('auth:sanctum');
Route::post('follow/pendings', [App\Http\Controllers\FollowersController::class, 'Pendings'])->middleware('auth:sanctum');
Route::post('follow/changestatus', [App\Http\Controllers\FollowersController::class, 'FollowChangeStatus'])->middleware('auth:sanctum');
Route::post('activity/list', [App\Http\Controllers\ActivityController::class, 'Activites'])->middleware('auth:sanctum');

Route::post('weblogin', [App\Http\Controllers\AuthController::class, 'weblogin']);

Route::post('webprofile', [App\Http\Controllers\AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('admin/users', [App\Http\Controllers\AdminController::class, 'UsersList'])->middleware('auth:sanctum');
Route::post('admin/singleuser', [App\Http\Controllers\AdminController::class, 'SingleUser'])->middleware('auth:sanctum');
Route::post('admin/posts', [App\Http\Controllers\AdminController::class, 'Posts'])->middleware('auth:sanctum');
Route::post('admin/stories', [App\Http\Controllers\AdminController::class, 'Stories'])->middleware('auth:sanctum');
Route::post('admin/users/disable', [App\Http\Controllers\AdminController::class, 'DisableUser'])->middleware('auth:sanctum');

// Reset Data base for delete stories
