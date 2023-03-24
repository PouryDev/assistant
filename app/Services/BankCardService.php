<?php

namespace App\Services;

use App\Models\BankCard;
use Ramsey\Uuid\Uuid;

class BankCardService
{
    private ?BankCard $card;

    public function __construct(string|BankCard $card = null)
    {
        if ($card instanceof BankCard) {
            $this->card = $card;
        } elseif (is_string($card)) {
            $this->card = BankCard::find($card);
        }
    }

    /**
     * Get bank card from service
     *
     * @return BankCard|null
     */
    public function get(): ?BankCard
    {
        return $this->card;
    }

    /**
     * Store new bank card in db
     *
     * @param string $title
     * @param string $number
     * @param int $userID
     * @return null|string
     */
    public function create(string $title, string $number, int $userID): ?string
    {
        $id = Uuid::uuid4()->toString();
        # Store bank card in db and return false on failure
        $card = BankCard::create([
            'id' => $id,
            'title' => $title,
            'number' => $number,
            'user_id' => $userID,
        ]);
        if (empty($card)) {
            $this->card = null;
            return '';
        }

        # Initialize card property
        $this->card = $card;

        return $id;
    }

    /**
     * Update existing bank card in db
     *
     * @param string $title
     * @param string $number
     * @return bool
     */
    public function update(string $title, string $number): bool
    {
        return (bool)$this->card->update([
            'title' => $title,
            'number' => $number,
        ]);
    }

    /**
     * Delete bank card from db if it has no transactions
     *
     * @return bool
     */
    public function delete(): bool
    {
        # Check if bank card has no transactions
        $haveTransactions = $this->card->transactions()->exists();
        if ($haveTransactions) {
            return false;
        }

        return (bool)$this->card->delete();
    }
}