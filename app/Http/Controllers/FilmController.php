<?php

namespace App\Http\Controllers;

use App\Events\FilmCreated;
use App\Http\Resources\FilmCollection;
use App\Models\Comment;
use App\Models\Film;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Film as FilmResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FilmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return FilmCollection
     */
    public function index(): FilmCollection
    {
        return new FilmCollection(Film::paginate(1));
    }

    /**
     * Get a validator for an incoming film creation request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'release_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'rating' => ['required', 'digits_between:1,5'],
            'price' => ['required', 'numeric', 'min:1'],
            'country' => ['required', 'exists:countries,id'],
            'photo' => ['mimes:jpg,jpeg,png'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Film
     */
    protected function createFilm(array $data): Film
    {
        return Film::create([
            'country_id' => $data['country'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name'], '-'),
            'description' => $data['description'],
            'release_date' => Carbon::parse($data['release_date'])->format('Y-m-d h:i:s'),
            'rating' => $data['rating'],
            'price' => $data['price'],
            'photo' => $data['photo_path'] ?? 'placeholder.png',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return FilmResource
     * @throws ValidationException
     */
    public function store(Request $request): FilmResource
    {
        $this->validator($request->all())->validate();

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $path = $request->file('photo')->store('public/film-images');
            $path = explode('/', $path);
            $request->request->add(['photo_path' => $path[2]]);
        }

        event(new FilmCreated($film = $this->createFilm($request->all())));

        return new FilmResource($film);
    }

    /**
     * Display the specified resource.
     *
     * @param $slug
     * @return FilmResource
     */
    public function show($slug): FilmResource
    {
        $item = Film::where('slug', $slug)->first();
        return new FilmResource($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        try {
            Film::destroy($id);
        } catch (Exception $e) {
            return new Response('', 204);
        }
        return new Response('', 204);
    }

    /**
     * Add comment.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function comment(Request $request) :JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'film_id' => 'required|exists:films,slug',
            'name' => 'required',
            'comment' => 'required',
        ], [
            'film_id.required' => 'Film reference not found.'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first()
                ],
                403
            );
        }

        $film = Film::where('slug', $request->film_id)->first();

        $comment = new Comment;
        $comment->film_id = $film->id;
        $comment->user_id = $request->user()->id;
        $comment->name = $request->name;
        $comment->comment = $request->comment;

        if ($comment->save()) {
            return response()->json([
                'message' => 'Comment added successfully.'
            ]);
        }

        return response()->json(
            [
                'message' => 'Something went wrong. Contact administrator.'
            ],
            404
        );
    }
}
