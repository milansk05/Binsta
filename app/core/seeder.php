<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/database.php';

use RedBeanPHP\R as R;

// Reset database
R::nuke();

// Standaard profielfoto
$default_profile_image = 'placeholder_image.png';

// Gebruikers aanmaken
$user1 = R::dispense('user');
$user1->username = 'future-tech-leader';
$user1->email = 'tech@example.com';
$user1->password = password_hash('password123', PASSWORD_DEFAULT);
$user1->bio = 'JavaScript enthusiast and web developer';
$user1->profile_image = $default_profile_image;
$user1->created_at = date('Y-m-d H:i:s');
$user1Id = R::store($user1);

$user2 = R::dispense('user');
$user2->username = 'code_wizard';
$user2->email = 'wizard@example.com';
$user2->password = password_hash('password123', PASSWORD_DEFAULT);
$user2->bio = 'Full-stack developer and open source contributor';
$user2->profile_image = $default_profile_image;
$user2->created_at = date('Y-m-d H:i:s');
$user2Id = R::store($user2);

// JavaScript functional programming post
$post1 = R::dispense('post');
$post1->user_id = $user1Id;
$post1->code = "const pluckDeep = key => obj => key.split('.').reduce((accum, key) => accum[key], obj)

const compose = (...fns) => res => fns.reduce((accum, next) => next(accum), res)

const unfold = (f, seed) => {
  const go = (f, seed, acc) => {
    const res = f(seed)
    return res ? go(f, res[1], acc.concat([res[0]])) : acc
  }
  return go(f, seed, [])
}";
$post1->language = 'javascript';
$post1->caption = 'Love these array functions in JavaScript 😍';
$post1->created_at = date('Y-m-d H:i:s', strtotime('-8 hours'));
$post1Id = R::store($post1);

// PHP Deck class post
$post2 = R::dispense('post');
$post2->user_id = $user1Id;
$post2->code = "<?php
declare(strict_types=1);

final class Deck
{
    private array \$cards = [];
    
    public function __construct()
    {
        foreach (['hearts', 'diamonds', 'clubs', 'spades'] as \$suit) {
            foreach (['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'] as \$value) {
                \$this->cards[] = ['suit' => \$suit, 'value' => \$value];
            }
        }
        \$this->shuffle();
    }
    
    // By shuffling the cards, we can take cards from the top, and they're still random.
    public function shuffle(): void
    {
        shuffle(\$this->cards);
    }
    
    public function drawCard(): ?array
    {
        if (empty(\$this->cards)) {
            throw new \Exception('No more cards in deck...');
        }
        
        return array_pop(\$this->cards);
    }
}";
$post2->language = 'php';
$post2->caption = 'Simple Deck class in PHP with strict types';
$post2->created_at = date('Y-m-d H:i:s', strtotime('-2 days'));
$post2Id = R::store($post2);

// Python post
$post3 = R::dispense('post');
$post3->user_id = $user2Id;
$post3->code = "test post lol";
$post3->language = 'python';
$post3->caption = 'Clean fibonacci implementation in Python';
$post3->created_at = date('Y-m-d H:i:s', strtotime('-1 day'));
$post3Id = R::store($post3);

// Likes toevoegen
$like1 = R::dispense('like');
$like1->post_id = $post1Id;
$like1->user_id = $user2Id;
$like1->created_at = date('Y-m-d H:i:s');
R::store($like1);

$like2 = R::dispense('like');
$like2->post_id = $post2Id;
$like2->user_id = $user2Id;
$like2->created_at = date('Y-m-d H:i:s');
R::store($like2);

// Commentaar toevoegen
$comment1 = R::dispense('comment');
$comment1->post_id = $post1Id;
$comment1->user_id = $user2Id;
$comment1->content = 'Nice! I love functional programming techniques.';
$comment1->created_at = date('Y-m-d H:i:s', strtotime('-7 hours'));
R::store($comment1);

$comment2 = R::dispense('comment');
$comment2->post_id = $post3Id;
$comment2->user_id = $user1Id;
$comment2->content = 'Elegant solution!';
$comment2->created_at = date('Y-m-d H:i:s', strtotime('-12 hours'));
R::store($comment2);

echo "Database seeding complete!\n";
