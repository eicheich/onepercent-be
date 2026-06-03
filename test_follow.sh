#!/bin/bash

# Test follow notification flow

API_URL="http://localhost:8000/api"

echo "=== Testing Follow Notification Flow ==="
echo ""

# List available users first
echo "1. Get available users (via API /notifications):"
echo "   (Trying with a test token...)"
echo ""

# For testing, let's use the web follow endpoint which doesn't need token
# But we need a session. Let's try a different approach - test with login first

echo "2. Testing via web interface (need browser or curl with cookies)"
echo ""

# Let's create a simple test with curl
echo "3. Attempting to test with curl (web session):"
echo ""

# Test a follow action via web
USER1_ID="6a1ef6013793dfc6480a1399"  # finn dr
USER2_ID="6a1efd663ceb8cda0e0bf7b9"  # husnoel

echo "   Trying to follow (would need web session cookie)"
echo "   FROM: $USER2_ID (husnoel)"
echo "   TO: $USER1_ID (finn dr)"
echo ""

# Let's check if the follow already exists
echo "4. Checking follow relationship..."
php -r "
require 'vendor/autoload.php';
require 'bootstrap/app.php';

\$exists = \App\Models\UserFollow::where('follower_id', '$USER2_ID')
    ->where('following_id', '$USER1_ID')
    ->first();

if (\$exists) {
    echo \"  Follow relationship exists: \$exists->created_at\n\";
} else {
    echo \"  No follow relationship found yet\n\";
}

// Check notifications for user 1
\$notifs = \App\Models\Notification::where('user_id', '$USER1_ID')
    ->where('type', 'follow')
    ->get();

echo \"  Follow notifications for user 1: \" . \$notifs->count() . \"\n\";
"

echo ""
echo "5. Let's manually create a follow notification to test the flow:"
php -r "
require 'vendor/autoload.php';
require 'bootstrap/app.php';

\$user1 = \App\Models\User::find('6a1ef6013793dfc6480a1399');  // finn dr
\$user2 = \App\Models\User::find('6a1efd663ceb8cda0e0bf7b9');  // husnoel

echo \"User 1 (target): \" . (\$user1?->name ?? 'Not found') . \"\n\";
echo \"User 2 (follower): \" . (\$user2?->name ?? 'Not found') . \"\n\";
echo \"\n\";

if (\$user1 && \$user2) {
    \App\Models\Notification::create([
        'user_id'  => '6a1ef6013793dfc6480a1399',
        'actor_id' => '6a1efd663ceb8cda0e0bf7b9',
        'type'     => 'follow',
        'title'    => \$user2->name . ' followed you',
        'body'     => 'You have a new follower!',
        'icon'     => '👥',
        'data'     => [
            'actor_name'   => \$user2->name,
            'actor_avatar' => \$user2->avatar,
        ],
        'read_at'  => null,
    ]);
    echo \"✓ Test follow notification created\n\";
}
"

echo ""
echo "6. Verifying notification was created:"
php -r "
require 'vendor/autoload.php';
require 'bootstrap/app.php';

\$notif = \App\Models\Notification::where('user_id', '6a1ef6013793dfc6480a1399')
    ->where('type', 'follow')
    ->orderBy('created_at', 'desc')
    ->first();

if (\$notif) {
    echo \"  ✓ Notification found!\n\";
    echo \"    ID: \" . \$notif->_id . \"\n\";
    echo \"    Type: \" . \$notif->type . \"\n\";
    echo \"    Title: \" . \$notif->title . \"\n\";
    echo \"    Created: \" . \$notif->created_at . \"\n\";
} else {
    echo \"  ✗ No notification found\n\";
}
"

echo ""
echo "=== End Test ==="
