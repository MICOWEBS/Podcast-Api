<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Podcast Platform API",
 *     description="A comprehensive API for managing podcasts, episodes, and categories. This API provides endpoints for user authentication, podcast management, and content discovery.",
 *     @OA\Contact(
 *         email="support@podcastplatform.com",
 *         name="API Support",
 *         url="https://podcastplatform.com/support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Use your API token as: Bearer {token}"
 * )
 * 
 * @OA\Schema(
 *     schema="LoginRequest",
 *     type="object",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     type="object",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123"),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="AuthResponse",
 *     type="object",
 *     @OA\Property(property="token", type="string", example="1|abcdef123456..."),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Technology"),
 *     @OA\Property(property="slug", type="string", example="technology"),
 *     @OA\Property(property="podcast_count", type="integer", example=10),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Podcast",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="The Tech Show"),
 *     @OA\Property(property="description", type="string", example="A weekly podcast about technology and innovation"),
 *     @OA\Property(property="image_url", type="string", format="uri", example="https://example.com/images/tech-show.jpg"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="episode_count", type="integer", example=50),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="Episode",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Episode 1: Introduction"),
 *     @OA\Property(property="description", type="string", example="In this episode, we introduce the show and its hosts"),
 *     @OA\Property(property="audio_url", type="string", format="uri", example="https://example.com/audio/ep1.mp3"),
 *     @OA\Property(property="duration", type="integer", example=3600, description="Duration in seconds"),
 *     @OA\Property(property="podcast_id", type="integer", example=1),
 *     @OA\Property(property="podcast", ref="#/components/schemas/Podcast"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 * 
 * @OA\Schema(
 *     schema="ForgotPasswordRequest",
 *     type="object",
 *     required={"email"},
 *     @OA\Property(property="email", type="string", format="email")
 * )
 * 
 * @OA\Schema(
 *     schema="ResetPasswordRequest",
 *     type="object",
 *     required={"token", "email", "password", "password_confirmation"},
 *     @OA\Property(property="token", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 *     @OA\Property(property="password_confirmation", type="string", format="password")
 * )
 * 
 * @OA\Schema(
 *     schema="MessageResponse",
 *     type="object",
 *     @OA\Property(property="message", type="string")
 * )
 * 
 * @OA\Schema(
 *     schema="PaginatedResponse",
 *     type="object",
 *     @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="meta", type="object",
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=5),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="to", type="integer", example=15),
 *         @OA\Property(property="total", type="integer", example=75)
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     type="object",
 *     required={"name"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Technology",
 *         description="The name of the category"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Technology related podcasts",
 *         description="A description of the category"
 *     )
 * )
 */
class Documentation
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register new user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login user",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    public function login() {}

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout() {}

    /**
     * @OA\Get(
     *     path="/api/podcasts",
     *     summary="Get all podcasts",
     *     tags={"Podcasts"},
     *     @OA\Response(
     *         response=200,
     *         description="List of podcasts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Podcast")
     *         )
     *     )
     * )
     */
    public function listPodcasts() {}

    /**
     * @OA\Get(
     *     path="/api/podcasts/{id}",
     *     summary="Get a specific podcast",
     *     tags={"Podcasts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of podcast",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast details",
     *         @OA\JsonContent(ref="#/components/schemas/Podcast")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast not found"
     *     )
     * )
     */
    public function showPodcast() {}

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             )
     *         )
     *     )
     * )
     */
    public function listCategories() {}

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get category details",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category details",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function showCategory() {}

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function createCategory() {}

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Category")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateCategory() {}

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Category deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot delete category with associated podcasts"
     *     )
     * )
     */
    public function deleteCategory() {}

    /**
     * @OA\Get(
     *     path="/api/episodes/{id}",
     *     summary="Get a specific episode",
     *     tags={"Episodes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of episode",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Episode details",
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Episode not found"
     *     )
     * )
     */
    public function showEpisode() {}

    /**
     * @OA\Post(
     *     path="/api/auth/forgot-password",
     *     summary="Request password reset link",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ForgotPasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function forgotPassword() {}

    /**
     * @OA\Post(
     *     path="/api/auth/reset-password",
     *     summary="Reset password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResetPasswordRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired token"
     *     )
     * )
     */
    public function resetPassword() {}

    /**
     * @OA\Get(
     *     path="/api/v1/categories/by-slug/{slug}",
     *     summary="Get category by slug",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Category slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Technology"),
     *                 @OA\Property(property="slug", type="string", example="technology"),
     *                 @OA\Property(property="description", type="string", example="Technology related podcasts"),
     *                 @OA\Property(property="podcasts_count", type="integer", example=5),
     *                 @OA\Property(property="podcasts", type="array", @OA\Items(ref="#/components/schemas/Podcast"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found"
     *     )
     * )
     */
    public function getCategoryBySlug() {}
} 