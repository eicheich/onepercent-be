
// ============================================
// STEP 1 — Buat dummy users
// ============================================
const users = [
  { name: "Aria Putri",    email: "aria@test.com",    current_streak: 1064, longest_streak: 1064 },
  { name: "Budi Santoso",  email: "budi@test.com",    current_streak: 1022, longest_streak: 1022 },
  { name: "Citra Dewi",    email: "citra@test.com",   current_streak: 987,  longest_streak: 987  },
  { name: "Dani Rahmat",   email: "dani@test.com",    current_streak: 798,  longest_streak: 798  },
  { name: "Eva Susanti",   email: "eva@test.com",     current_streak: 693,  longest_streak: 693  },
  { name: "Fajar Nugroho", email: "fajar@test.com",   current_streak: 658,  longest_streak: 658  },
  { name: "Gita Lestari",  email: "gita@test.com",    current_streak: 616,  longest_streak: 616  },
]

const userIds = []
for (const u of users) {
  const result = db.users.insertOne({
    name: u.name,
    email: u.email,
    password: "$2y$12$dummyhashedpassword",
    current_streak: u.current_streak,
    longest_streak: u.longest_streak,
    last_completed_date: new Date().toISOString().split('T')[0],
    gender: null,
    birth_date: null,
    avatar: null,
    created_at: new Date(),
    updated_at: new Date()
  })
  userIds.push(result.insertedId)
  print("Created user: " + u.name + " -> " + result.insertedId)
}

// ============================================
// STEP 2 — Personalization tags untuk tiap user
// ============================================
const tagSets = [
  ["technology", "business"],
  ["health", "sports"],
  ["finance", "education"],
  ["lifestyle", "technology"],
  ["entertainment", "health"],
  ["business", "finance"],
  ["education", "lifestyle"],
]

for (let i = 0; i < userIds.length; i++) {
  db.user_personalizations.insertOne({
    user_id: userIds[i].toString(),
    tags: tagSets[i],
    created_at: new Date(),
    updated_at: new Date()
  })
}
print("Personalization tags created!")

// ============================================
// STEP 3 — Challenges pool
// ============================================
const challenges = [
  {
    title: "Design a simple UI using HTML & CSS",
    content: "Improve your layout skills and learn how to build responsive designs.",
    estimated_minutes: 10,
    tags: ["technology", "education"],
    signature: "abc123design"
  },
  {
    title: "Track your daily expenses",
    content: "Write down all your expenses today and categorize them. Find 1 area to cut back.",
    estimated_minutes: 15,
    tags: ["finance", "lifestyle"],
    signature: "abc123finance"
  },
  {
    title: "30-minute morning workout",
    content: "Do a quick home workout — 20 pushups, 30 squats, 1 min plank. Repeat 2x.",
    estimated_minutes: 30,
    tags: ["health", "sports"],
    signature: "abc123health"
  },
  {
    title: "Read 10 pages of a book",
    content: "Pick a book you've been meaning to read and read 10 pages. Write 3 key takeaways.",
    estimated_minutes: 20,
    tags: ["education", "lifestyle"],
    signature: "abc123read"
  },
  {
    title: "Plan your week ahead",
    content: "Write down your top 3 goals for this week and break them into daily tasks.",
    estimated_minutes: 15,
    tags: ["business", "lifestyle"],
    signature: "abc123plan"
  }
]

const challengeIds = []
for (const c of challenges) {
  const result = db.challenges.insertOne({
    ...c,
    metadata: { provider: "dummy" },
    created_at: new Date(),
    updated_at: new Date()
  })
  challengeIds.push(result.insertedId)
}
print("Challenges created!")

// ============================================
// STEP 4 — Daily challenge history (7 hari)
// ============================================
const today = new Date()

for (let u = 0; u < userIds.length; u++) {
  for (let d = 6; d >= 0; d--) {
    const date = new Date(today)
    date.setDate(today.getDate() - d)
    const dateStr = date.toISOString().split('T')[0]

    // User dengan streak besar = completed semua hari
    // User dengan streak kecil = skip beberapa hari
    const shouldComplete = u < 3 ? true : (d % 2 === 0)

    const challengeId = challengeIds[u % challengeIds.length]

    db.user_daily_challenges.insertOne({
      user_id: userIds[u].toString(),
      challenge_id: challengeId.toString(),
      challenge_date: dateStr,
      is_completed: shouldComplete,
      completed_at: shouldComplete ? date : null,
      expires_at: new Date(date.getTime() + 24 * 60 * 60 * 1000),
      metadata: { provider: "dummy" },
      created_at: date,
      updated_at: date
    })
  }
}
print("Daily challenge history created!")

// ============================================
// STEP 5 — Follow relationships (mutual)
// ============================================
// User 0 follow semua, semua follow user 0
for (let i = 1; i < userIds.length; i++) {
  db.user_follows.insertOne({
    follower_id: userIds[0].toString(),
    following_id: userIds[i].toString(),
    created_at: new Date()
  })
  db.user_follows.insertOne({
    follower_id: userIds[i].toString(),
    following_id: userIds[0].toString(),
    created_at: new Date()
  })
}
print("Follow relationships created!")

// ============================================
// STEP 6 — Poke notifications
// ============================================
db.challenge_pokes.insertOne({
  sender_id: userIds[1].toString(),
  receiver_id: userIds[0].toString(),
  user_daily_challenge_id: "dummy",
  challenge_id: challengeIds[0].toString(),
  type: "boast",
  message: "I finished my challenge today! 💪",
  metadata: {
    challenge_title: "Design a simple UI using HTML & CSS",
    challenge_date: today.toISOString().split('T')[0]
  },
  read_at: null,
  created_at: new Date()
})

db.challenge_pokes.insertOne({
  sender_id: userIds[2].toString(),
  receiver_id: userIds[0].toString(),
  user_daily_challenge_id: "dummy",
  challenge_id: challengeIds[1].toString(),
  type: "remind",
  message: "Hey! Don't forget your challenge today 👀",
  metadata: {
    challenge_title: "Track your daily expenses",
    challenge_date: today.toISOString().split('T')[0]
  },
  read_at: null,
  created_at: new Date(Date.now() - 15 * 60 * 1000) // 15 menit lalu
})

print("Poke notifications created!")

// ============================================
// STEP 7 — Verifikasi semua data
// ============================================
print("\n=== DATA SUMMARY ===")
print("Users: " + db.users.countDocuments())
print("Personalizations: " + db.user_personalizations.countDocuments())
print("Challenges: " + db.challenges.countDocuments())
print("Daily Challenges: " + db.user_daily_challenges.countDocuments())
print("Follows: " + db.user_follows.countDocuments())
print("Pokes: " + db.challenge_pokes.countDocuments())
print("===================")
print("\nDone! Dummy data created successfully 🎉")
