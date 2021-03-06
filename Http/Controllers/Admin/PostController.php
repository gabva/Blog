<?php namespace Modules\Blog\Http\Controllers\Admin;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Laracasts\Flash\Flash;
use Modules\Blog\Entities\Post;
use Modules\Blog\Http\Requests\StorePostRequest;
use Modules\Blog\Http\Requests\UpdatePostRequest;
use Modules\Blog\Repositories\CategoryRepository;
use Modules\Blog\Repositories\PostRepository;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Repositories\FileRepository;

class PostController extends AdminBaseController
{
    /**
     * @var PostRepository
     */
    private $post;
    /**
     * @var CategoryRepository
     */
    private $category;
    /**
     * @var FileRepository
     */
    private $file;

    public function __construct(PostRepository $post, CategoryRepository $category, FileRepository $file)
    {
        parent::__construct();

        $this->post = $post;
        $this->category = $category;
        $this->file = $file;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $posts = $this->post->all();

        return View::make('blog::admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = $this->category->allTranslatedIn(App::getLocale());

        return View::make('blog::admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StorePostRequest $request
     * @return Response
     */
    public function store(StorePostRequest $request)
    {
        $this->post->create($request->all());

        Flash::success(trans('blog::messages.post created'));

        return Redirect::route('admin.blog.post.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post     $post
     * @return Response
     */
    public function edit(Post $post)
    {
        $thumbnail = $this->file->findFileByZoneForEntity('thumbnail', $post);
        $categories = $this->category->allTranslatedIn(App::getLocale());

        return View::make('blog::admin.posts.edit', compact('post', 'categories', 'thumbnail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Post              $post
     * @param UpdatePostRequest $request
     * @return
     */
    public function update(Post $post, UpdatePostRequest $request)
    {
        $this->post->update($post, $request->all());

        Flash::success(trans('blog::messages.post updated'));

        return Redirect::route('admin.blog.post.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Post     $post
     * @return Response
     */
    public function destroy(Post $post)
    {
        $post->tags()->detach();

        $this->post->destroy($post);

        Flash::success(trans('blog::messages.post deleted'));

        return Redirect::route('admin.blog.post.index');
    }
}
