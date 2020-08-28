<?php

namespace App\Http\Controllers\API\ExternalServices;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

/*
 * Fake user server that should receive a message from marketplace about sold key.
 * */

class UserSalesManagementController extends Controller
{
    /**
     * @OA\Post(
     * path="/user/{user:username}/sales",
     * summary="User Server",
     * description="Send information about purchase to the user server. It should not be done manually, because it's done automatically on purchase confirmation or by the scheduled job. This server responses with an error with 50% chance.",
     * operationId="userSalesStore",
     * tags={"Fake External Services"},
     *   @OA\Parameter(
     *      name="user:username",
     *      in="path",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     * @OA\RequestBody(
     *    required=false,
     *    description="Message",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Any data to send to the server for testing it."),
     *    ),
     * ),
     *  @OA\Response(
     *      response=201,
     *      description="Key sale has been processed.",
     *  ),
     *  @OA\Response(
     *      response=503,
     *      description="Whops, something went wrong. Try again later.",
     *  ),
     *  @OA\Response(
     *     response=404,
     *     description="Specified user has not been found",
     *  ),
     * )
     * )
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        return now()->timestamp % 2 === 0 ? response('Key sale has been processed.', 201) : response('Whops, something went wrong. Try again later.', 503);
    }
}
