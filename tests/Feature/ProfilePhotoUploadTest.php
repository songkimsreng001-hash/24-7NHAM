<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProfilePhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // Clean up any test avatars generated in public/img/avatars
        $testFiles = glob(public_path('img/avatars/avatar_*_test_*'));
        if ($testFiles) {
            foreach ($testFiles as $file) {
                @unlink($file);
            }
        }
        parent::tearDown();
    }

    public function test_edit_profile_page_renders_with_avatar_upload_input(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '012345678',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit', $user->id));

        $response->assertOk();
        $response->assertSee('Profile Photo');
        $response->assertSee('id="avatarInput"', false);
        $response->assertSee('enctype="multipart/form-data"', false);
        $response->assertSee('.PNG, .JPG, .JPEG, .WEBP', false);
    }

    private function getFakeImageContent(string $ext): string
    {
        $ext = strtolower($ext);

        return match ($ext) {
            'png' => base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='),
            'jpg', 'jpeg' => base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='),
            'webp' => base64_decode('UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA=='),
            default => 'dummy content',
        };
    }

    public function test_user_can_upload_lowercase_image_extensions(): void
    {
        $user = User::factory()->create([
            'name' => 'Sokha',
            'email' => 'sokha@example.com',
            'phone' => '012999888',
        ]);

        $extensions = ['png', 'jpg', 'jpeg', 'webp'];

        foreach ($extensions as $ext) {
            $content = $this->getFakeImageContent($ext);
            $file = UploadedFile::fake()->createWithContent("photo.{$ext}", $content);

            $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $file,
            ]);

            $response->assertRedirect();
            $response->assertSessionHas('success');

            $user->refresh();
            $this->assertNotNull($user->avatar);
            $this->assertStringEndsWith(".{$ext}", $user->avatar);

            // Clean up the created file
            if (file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }
        }
    }

    public function test_user_can_upload_uppercase_image_extensions(): void
    {
        $user = User::factory()->create([
            'name' => 'Thalin',
            'email' => 'thalin@example.com',
            'phone' => '011222333',
        ]);

        $uppercaseExtensions = ['PNG', 'JPG', 'JPEG', 'WEBP'];

        foreach ($uppercaseExtensions as $ext) {
            $content = $this->getFakeImageContent($ext);
            $file = UploadedFile::fake()->createWithContent("MY_PHOTO.{$ext}", $content);

            $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $file,
            ]);

            $response->assertRedirect();
            $response->assertSessionHas('success');

            $user->refresh();
            $this->assertNotNull($user->avatar);
            // Stored filename normalizes extension to lowercase for web asset URLs
            $this->assertStringEndsWith('.'.strtolower($ext), $user->avatar);

            // Clean up the created file
            if (file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }
        }
    }

    public function test_disallowed_file_types_are_rejected(): void
    {
        $user = User::factory()->create([
            'name' => 'Reject Test',
            'email' => 'reject@example.com',
            'phone' => '088777666',
        ]);

        // Disallowed types: pdf, txt, gif
        $disallowedFiles = [
            UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('notes.txt', 50, 'text/plain'),
            UploadedFile::fake()->create('animation.gif', 100, 'image/gif'),
            UploadedFile::fake()->create('ANIMATION.GIF', 100, 'image/gif'),
        ];

        foreach ($disallowedFiles as $file) {
            $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $file,
            ]);

            $response->assertSessionHasErrors('avatar');
        }
    }

    public function test_user_can_remove_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Remove Avatar User',
            'email' => 'remove@example.com',
            'phone' => '099111222',
            'avatar' => 'dummy_avatar.jpg',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'remove_avatar' => 1,
        ]);

        $response->assertRedirect();
        $user->refresh();
        $this->assertNull($user->avatar);
    }

    public function test_avatar_url_attribute_accessor(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'phone' => '012345678',
            'avatar' => null,
        ]);

        // When null, fallback to UI avatars
        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
        $this->assertStringContainsString('John+Doe', $user->avatar_url);

        // When external URL (e.g. Google OAuth)
        $user->avatar = 'https://lh3.googleusercontent.com/a/testphoto';
        $this->assertEquals('https://lh3.googleusercontent.com/a/testphoto', $user->avatar_url);
    }

    public function test_edit_profile_page_renders_with_crop_modal_and_avatar_circle(): void
    {
        $user = User::factory()->create([
            'name' => 'Crop View User',
            'email' => 'cropview@example.com',
            'phone' => '012888777',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit', $user->id));

        $response->assertOk();
        $response->assertSee('id="profileAvatarCircle"', false);
        $response->assertSee('id="cropImageModal"', false);
        $response->assertSee('id="cropImageTarget"', false);
        $response->assertSee('id="btnApplyCrop"', false);
        $response->assertSee('cropper.min.css', false);
        $response->assertSee('cropper.min.js', false);
    }

    public function test_user_can_upload_cropped_avatar_data_url(): void
    {
        $user = User::factory()->create([
            'name' => 'Cropped User',
            'email' => 'cropped@example.com',
            'phone' => '077123456',
        ]);

        $formats = [
            'jpeg' => 'data:image/jpeg;base64,'.base64_encode($this->getFakeImageContent('jpg')),
            'png' => 'data:image/png;base64,'.base64_encode($this->getFakeImageContent('png')),
            'webp' => 'data:image/webp;base64,'.base64_encode($this->getFakeImageContent('webp')),
        ];

        foreach ($formats as $ext => $dataUrl) {
            $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'cropped_avatar_data' => $dataUrl,
            ]);

            $response->assertRedirect();
            $response->assertSessionHas('success');

            $user->refresh();
            $this->assertNotNull($user->avatar);
            $expectedExt = ($ext === 'jpeg') ? 'jpg' : $ext;
            $this->assertStringEndsWith('.'.$expectedExt, $user->avatar);

            // Verify file actually exists in public/img/avatars
            $this->assertFileExists(public_path('img/avatars/'.$user->avatar));

            // Clean up
            @unlink(public_path('img/avatars/'.$user->avatar));
        }
    }

    public function test_invalid_cropped_avatar_format_is_rejected(): void
    {
        $user = User::factory()->create([
            'name' => 'Invalid Cropped User',
            'email' => 'invalidcropped@example.com',
            'phone' => '077999111',
        ]);

        $invalidData = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        $response = $this->actingAs($user)->patch(route('profile.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'cropped_avatar_data' => $invalidData,
        ]);

        $response->assertSessionHasErrors('cropped_avatar_data');
    }
}
