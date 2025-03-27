<?php

namespace App\Twig\Components;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[AsLiveComponent]
final class ProfileForm
{
    use DefaultActionTrait;

    private ?User $user = null;

    /**
     * ALERT MESSAGE STATE
     */
    #[LiveProp(writable: true)]
    public bool $isShowingAlert = false;

    #[LiveProp(writable: true)]
    public string $alertMessage = '';

    #[LiveProp(writable: true)]
    public string $alertType = '';

    /**
     * IS EDITING STATE
     */
    #[LiveProp(writable: true)]
    public bool $isEditingProfile = false;

    #[LiveProp(writable: true)]
    public bool $isEditingAvatar = false;

    /**
     * PROFILE STATE
     */
    #[LiveProp(writable: true)]
    public string $firstName;

    #[LiveProp(writable: true)]
    public string $lastName = '';

    #[LiveProp(writable: true)]
    public string $email = '';

    #[LiveProp(writable: true)]
    public string $avatarUrl = '';

    /**
     * MINIMUM PASSWORD LENGTH
     */
    #[LiveProp]
    public int $passwordMinLength;

    /**
     * PASSWORD STATE
     */
    #[LiveProp(writable: true)]
    public bool $isEditingPassword = false;

    #[LiveProp(writable: true)]
    public string $password = '';

    #[LiveProp(writable: true)]
    public string $passwordConfirm = '';

    /**
     * CONSTRUCTOR
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private Security $security,
        private ContainerBagInterface $params,
        private TranslatorInterface $translator,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
        $user = $this->security->getUser();
        if (!$user instanceof \App\Entity\User\User) {
            throw new \RuntimeException('User is not authenticated or is not of type App\Entity\User\User.');
        }
        $this->user = $user;
        $this->setProfileDefaults($this->user);
        $this->passwordMinLength = $this->params->get('password.minLength');
    }

    /**
     * TOGGLE EDITING STATES
     */
    #[LiveAction]
    public function toggleisEditingProfile(): void
    {
        $this->hideAlert();
        $this->isEditingProfile = !$this->isEditingProfile;
        $this->setProfileDefaults($this->user);
    }

    #[LiveAction]
    public function toggleIsEditingAvatar(): void
    {
        $this->hideAlert();
        $this->isEditingAvatar = !$this->isEditingAvatar;
        $this->setProfileDefaults($this->user);
    }

    #[LiveAction]
    public function toggleIsEditingPassword(): void
    {
        $this->hideAlert();
        $this->isEditingPassword = !$this->isEditingPassword;
        $this->setPasswordDefaults();
    }

    /**
     * SAVE PROFILE
     */
    #[LiveAction]
    public function saveProfile(): void
    {
        // Hide alert
        $this->hideAlert();

        // Validate profile data
        if ($this->firstName === '' || $this->lastName === '' || $this->email === '') {
            $this->showAlert($this->translator->trans('Please fill in all fields'), 'danger');
            return;
        }

        // Validate email
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->showAlert($this->translator->trans('Please enter a valid email address'), 'danger');
            return;
        }

        // Save profile
        $user = $this->userRepository->find($this->user->getId());
        $user->setFirstName($this->firstName);
        $user->setLastName($this->lastName);
        $user->setEmail($this->email);
        $this->entityManager->flush();

        // Hide alert (if any)
        $this->hideAlert();

        // Stop editing profile
        $this->isEditingProfile = false;

        // Set profile defaults
        $this->setProfileDefaults($user);
    }

    /**
     * SAVE AVATAR
     */
    #[LiveAction]
    public function saveAvatar(Request $request): void
    {
        // Hide alert
        $this->hideAlert();

        // Get file
        $file = $request->files->get('avatar');

        // Validate file
        if (!$file || !$file instanceof UploadedFile) {
            $this->showAlert($this->translator->trans('Please select a file'), 'danger');
            unlink($file->pathname);
            return;
        };

        if (!in_array($file->getMimeType(), $this->params->get('avatar.allowedMimeTypes'))) {
            $this->showAlert($this->translator->trans('File type not allowed'), 'danger');
            return;
        };

        if ($file->getSize() > $this->params->get('avatar.maxFileSize')) {
            $this->showAlert($this->translator->trans('File size is too large'), 'danger');
            return;
        };

        // Set file data
        $uploadDir = $this->params->get('avatar.uploadDir');
        $extension = $file->guessClientExtension();
        $fileName = uniqid($this->user->getId() . '-') . '.' . $extension;

        // Delete old avatar (if it exists)
        if ($this->user->getAvatarUrl()) {
            unlink($uploadDir . '/' . $this->user->getAvatarUrl());
        }

        // Save file
        $file->move($uploadDir, $fileName);

        // Update user
        $user = $this->userRepository->find($this->user->getId());
        $user->setAvatarUrl($fileName);
        $this->entityManager->flush();
        $this->setProfileDefaults($user);

        // Hide alert (if any)
        $this->hideAlert();

        // Stop editing profile
        $this->isEditingAvatar = false;

        // Set profile defaults
        $this->setAvatarDefaults($user);
    }

    /**
     * SAVE PASSWORD
     */
    #[LiveAction]
    public function savePassword(): void
    {
        // Hide alert (if any)
        $this->hideAlert();


        // Validate password
        if (strlen($this->password) < $this->passwordMinLength) {
            $this->isEditingPassword = false;
            $this->setPasswordDefaults();
            $this->showAlert(
                $this->translator->trans('Your password is not long enough'),
                'danger'
            );
            return;
        };

        if ($this->password !== $this->passwordConfirm) {
            $this->isEditingPassword = false;
            $this->setPasswordDefaults();
            $this->showAlert($this->translator->trans('Passwords do not match'), 'danger');
            return;
        };

        // Save password
        $user = $this->userRepository->find($this->user->getId());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $this->password));
        $this->entityManager->flush();
        $this->isEditingPassword = false;
        $this->setPasswordDefaults();

        // Hide alert (if any)
        $this->showAlert($this->translator->trans('Password updated'), 'success');

        // Stop editing password
        $this->isEditingPassword = false;

        // Set profile defaults
        $this->setPasswordDefaults();
    }

    /**
     * UTITILITIES
     */
    private function setAvatarDefaults(User $user): void
    {
        $this->avatarUrl = $user->getAvatarUrl() ?? '';
    }

    private function setProfileDefaults(User $user): void
    {
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
        $this->avatarUrl = $user->getAvatarUrl() ?? '';
    }

    private function setPasswordDefaults(): void
    {
        $this->password = '';
        $this->passwordConfirm = '';
    }

    private function showAlert(string $message, string $type): void
    {
        $this->isShowingAlert = true;
        $this->alertMessage = $message;
        $this->alertType = $type;
    }

    private function hideAlert(): void
    {
        $this->isShowingAlert = false;
        $this->alertMessage = '';
        $this->alertType = '';
    }
}
