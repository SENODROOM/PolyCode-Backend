# Go Strings

## String Basics

### String Declaration and Initialization
```go
package main

import "fmt"

func main() {
    // String literals
    var greeting string = "Hello, World!"
    var name string = "Go Programming"
    
    fmt.Printf("Greeting: %s\n", greeting)
    fmt.Printf("Name: %s\n", name)
    
    // Short declaration
    message := "Welcome to Go!"
    fmt.Printf("Message: %s\n", message)
    
    // Empty string
    var empty string = ""
    fmt.Printf("Empty string: '%s' (length: %d)\n", empty, len(empty))
    
    // String concatenation
    firstName := "John"
    lastName := "Doe"
    fullName := firstName + " " + lastName
    fmt.Printf("Full name: %s\n", fullName)
    
    // Multi-line string concatenation
    longMessage := "This is a long message " +
        "that spans multiple lines " +
        "for better readability."
    fmt.Printf("Long message: %s\n", longMessage)
    
    // Raw string literals
    rawString := `This is a raw string
with multiple lines
and special characters: \t \n \"`
    fmt.Printf("Raw string:\n%s\n", rawString)
    
    // String with escape sequences
    escaped := "Hello\tWorld!\nNew line here."
    fmt.Printf("Escaped string:\n%s\n", escaped)
    
    // Unicode strings
    unicode := "Hello 世界 🌍"
    fmt.Printf("Unicode string: %s\n", unicode)
    fmt.Printf("Unicode length: %d bytes, %d runes\n", len(unicode), len([]rune(unicode)))
    
    // String comparison
    str1 := "hello"
    str2 := "hello"
    str3 := "world"
    
    fmt.Printf("str1 == str2: %t\n", str1 == str2)
    fmt.Printf("str1 == str3: %t\n", str1 == str3)
    
    // String case comparison
    fmt.Printf("str1 == str2 (case-insensitive): %t\n", 
        strings.ToLower(str1) == strings.ToLower(str2))
    
    // String length
    text := "Hello, Go!"
    fmt.Printf("String: %s\n", text)
    fmt.Printf("Length (bytes): %d\n", len(text))
    fmt.Printf("Length (runes): %d\n", len([]rune(text)))
    
    // String indexing
    fmt.Printf("First character: %c\n", text[0])
    fmt.Printf("Last character: %c\n", text[len(text)-1])
    
    // String slicing
    substring := text[0:5] // "Hello"
    fmt.Printf("Substring [0:5]: %s\n", substring)
    
    substring2 := text[7:] // "Go!"
    fmt.Printf("Substring [7:]: %s\n", substring2)
    
    substring3 := text[:5] // "Hello"
    fmt.Printf("Substring [:5]: %s\n", substring3)
    
    // String immutability
    original := "hello"
    // original[0] = 'H' // This would cause a compile error
    
    // To modify a string, create a new one
    modified := "H" + original[1:]
    fmt.Printf("Original: %s\n", original)
    fmt.Printf("Modified: %s\n", modified)
    
    // String as slice of bytes
    bytes := []byte(text)
    fmt.Printf("Bytes: %v\n", bytes)
    
    // Convert bytes back to string
    fromBytes := string(bytes)
    fmt.Printf("From bytes: %s\n", fromBytes)
    
    // String with runes
    runes := []rune(unicode)
    fmt.Printf("Runes: %v\n", runes)
    
    // Convert runes back to string
    fromRunes := string(runes)
    fmt.Printf("From runes: %s\n", fromRunes)
}
```

### String Operations
```go
package main

import (
    "fmt"
    "strings"
    "strconv"
    "unicode"
)

func main() {
    // String operations
    
    // 1. Case conversion
    text := "Hello, World!"
    
    fmt.Printf("Original: %s\n", text)
    fmt.Printf("Lower: %s\n", strings.ToLower(text))
    fmt.Printf("Upper: %s\n", strings.ToUpper(text))
    fmt.Printf("Title: %s\n", strings.Title(text))
    
    // 2. Trimming
    padded := "   Hello, World!   "
    fmt.Printf("Padded: '%s'\n", padded)
    fmt.Printf("Trim: '%s'\n", strings.TrimSpace(padded))
    fmt.Printf("Trim left: '%s'\n", strings.TrimLeft(padded, " "))
    fmt.Printf("Trim right: '%s'\n", strings.TrimRight(padded, " "))
    
    // 3. Prefix and suffix
    prefix := "Hello, World!"
    fmt.Printf("HasPrefix 'Hello': %t\n", strings.HasPrefix(prefix, "Hello"))
    fmt.Printf("HasPrefix 'Hi': %t\n", strings.HasPrefix(prefix, "Hi"))
    fmt.Printf("HasSuffix '!': %t\n", strings.HasSuffix(prefix, "!"))
    fmt.Printf("HasSuffix '?': %t\n", strings.HasSuffix(prefix, "?"))
    
    // 4. Splitting
    sentence := "The quick brown fox jumps over the lazy dog"
    words := strings.Split(sentence, " ")
    fmt.Printf("Words: %v\n", words)
    
    // Split by multiple characters
    data := "apple,banana;cherry|date"
    parts := strings.FieldsFunc(data, func(c rune) bool {
        return c == ',' || c == ';' || c == '|'
    })
    fmt.Printf("Parts: %v\n", parts)
    
    // 5. Joining
    joined := strings.Join(words, "-")
    fmt.Printf("Joined: %s\n", joined)
    
    // 6. Repeating
    repeated := strings.Repeat("Go! ", 3)
    fmt.Printf("Repeated: %s\n", repeated)
    
    // 7. Replacing
    original := "Hello, World! Hello, Universe!"
    replaced := strings.Replace(original, "Hello", "Hi", 2)
    fmt.Printf("Replaced: %s\n", replaced)
    
    replacedAll := strings.ReplaceAll(original, "Hello", "Hi")
    fmt.Printf("Replaced all: %s\n", replacedAll)
    
    // 8. Counting
    text2 := "banana"
    count := strings.Count(text2, "a")
    fmt.Printf("Count of 'a' in '%s': %d\n", text2, count)
    
    // 9. Finding substrings
    sentence2 := "The quick brown fox jumps over the lazy dog"
    index := strings.Index(sentence2, "fox")
    fmt.Printf("Index of 'fox': %d\n", index)
    
    index2 := strings.Index(sentence2, "cat")
    fmt.Printf("Index of 'cat': %d\n", index2) // -1 if not found
    
    last := strings.LastIndex(sentence2, "the")
    fmt.Printf("Last index of 'the': %d\n", last)
    
    // 10. Contains
    contains := strings.Contains(sentence2, "fox")
    fmt.Printf("Contains 'fox': %t\n", contains)
    
    // 11. Comparing
    str1 := "apple"
    str2 := "banana"
    str3 := "apple"
    
    fmt.Printf("Compare '%s' and '%s': %d\n", str1, str2, strings.Compare(str1, str2))
    fmt.Printf("Compare '%s' and '%s': %d\n", str1, str3, strings.Compare(str1, str3))
    
    // 12. Character manipulation
    text3 := "Hello, World! 123"
    
    // Count specific characters
    letters := 0
    digits := 0
    spaces := 0
    
    for _, char := range text3 {
        if unicode.IsLetter(char) {
            letters++
        } else if unicode.IsDigit(char) {
            digits++
        } else if unicode.IsSpace(char) {
            spaces++
        }
    }
    
    fmt.Printf("Letters: %d, Digits: %d, Spaces: %d\n", letters, digits, spaces)
    
    // 13. String building
    var builder strings.Builder
    builder.WriteString("Hello")
    builder.WriteString(", ")
    builder.WriteString("World")
    builder.WriteString("!")
    
    built := builder.String()
    fmt.Printf("Built string: %s\n", built)
    
    // 14. String formatting
    name := "John"
    age := 30
    height := 1.75
    
    formatted := fmt.Sprintf("Name: %s, Age: %d, Height: %.2f", name, age, height)
    fmt.Printf("Formatted: %s\n", formatted)
    
    // 15. Type conversions
    strNum := "42"
    num, err := strconv.Atoi(strNum)
    if err == nil {
        fmt.Printf("Converted '%s' to int: %d\n", strNum, num)
    }
    
    strFloat := "3.14159"
    floatNum, err := strconv.ParseFloat(strFloat, 64)
    if err == nil {
        fmt.Printf("Converted '%s' to float: %f\n", strFloat, floatNum)
    }
    
    intNum := 123
    strFromInt := strconv.Itoa(intNum)
    fmt.Printf("Converted %d to string: '%s'\n", intNum, strFromInt)
    
    floatNum2 := 2.71828
    strFromFloat := strconv.FormatFloat(floatNum2, 'f', 5, 64)
    fmt.Printf("Converted %f to string: '%s'\n", floatNum2, strFromFloat)
    
    // 16. String validation
    email := "user@example.com"
    isValidEmail := validateEmail(email)
    fmt.Printf("Is '%s' valid email: %t\n", email, isValidEmail)
    
    // 17. String cleaning
    dirty := "  \t  Hello, World!  \n  "
    clean := strings.TrimSpace(dirty)
    fmt.Printf("Dirty: '%s'\n", dirty)
    fmt.Printf("Clean: '%s'\n", clean)
    
    // 18. String tokenization
    csv := "apple,banana,cherry,date"
    tokens := strings.Split(csv, ",")
    fmt.Printf("CSV tokens: %v\n", tokens)
    
    // 19. String padding
    text4 := "Go"
    paddedLeft := fmt.Sprintf("%10s", text4)
    paddedRight := fmt.Sprintf("%-10s", text4)
    
    fmt.Printf("Padded left: '%s'\n", paddedLeft)
    fmt.Printf("Padded right: '%s'\n", paddedRight)
    
    // 20. String reversal
    original := "Hello, World!"
    reversed := reverseString(original)
    fmt.Printf("Original: %s\n", original)
    fmt.Printf("Reversed: %s\n", reversed)
}

func validateEmail(email string) bool {
    return strings.Contains(email, "@") && strings.Contains(email, ".")
}

func reverseString(s string) string {
    runes := []rune(s)
    for i, j := 0, len(runes)-1; i < j; i, j = i+1, j-1 {
        runes[i], runes[j] = runes[j], runes[i]
    }
    return string(runes)
}
```

## String Manipulation

### Advanced String Operations
```go
package main

import (
    "fmt"
    "strings"
    "unicode"
    "regexp"
    "sort"
)

func main() {
    // Advanced string manipulation
    
    // 1. Palindrome check
    palindrome1 := "racecar"
    palindrome2 := "hello"
    
    fmt.Printf("Is '%s' palindrome: %t\n", palindrome1, isPalindrome(palindrome1))
    fmt.Printf("Is '%s' palindrome: %t\n", palindrome2, isPalindrome(palindrome2))
    
    // 2. Anagram check
    word1 := "listen"
    word2 := "silent"
    word3 := "hello"
    
    fmt.Printf("Are '%s' and '%s' anagrams: %t\n", word1, word2, areAnagrams(word1, word2))
    fmt.Printf("Are '%s' and '%s' anagrams: %t\n", word1, word3, areAnagrams(word1, word3))
    
    // 3. String permutation
    text := "abc"
    permutations := generatePermutations(text)
    fmt.Printf("Permutations of '%s': %v\n", text, permutations)
    
    // 4. Longest common prefix
    strings1 := []string{"flower", "flow", "flight"}
    strings2 := []string{"dog", "racecar", "car"}
    
    fmt.Printf("Longest common prefix of %v: '%s'\n", strings1, longestCommonPrefix(strings1))
    fmt.Printf("Longest common prefix of %v: '%s'\n", strings2, longestCommonPrefix(strings2))
    
    // 5. Longest common substring
    str1 := "ABABC"
    str2 := "BABCA"
    
    fmt.Printf("Longest common substring between '%s' and '%s': '%s'\n", 
        str1, str2, longestCommonSubstring(str1, str2))
    
    // 6. String compression
    original := "aaabbbcccaa"
    compressed := compressString(original)
    fmt.Printf("Original: '%s'\n", original)
    fmt.Printf("Compressed: '%s'\n", compressed)
    
    // 7. String decompression
    compressed2 := "a3b3c2a2"
    decompressed := decompressString(compressed2)
    fmt.Printf("Compressed: '%s'\n", compressed2)
    fmt.Printf("Decompressed: '%s'\n", decompressed)
    
    // 8. Word frequency analysis
    text := "the quick brown fox jumps over the lazy dog the fox was quick"
    frequencies := wordFrequency(text)
    fmt.Printf("Word frequencies: %v\n", frequencies)
    
    // 9. Remove duplicates
    withDuplicates := "apple banana apple cherry banana date apple"
    unique := removeDuplicateWords(withDuplicates)
    fmt.Printf("With duplicates: '%s'\n", withDuplicates)
    fmt.Printf("Unique words: '%s'\n", unique)
    
    // 10. Capitalize each word
    sentence := "the quick brown fox jumps over the lazy dog"
    titleCase := titleCase(sentence)
    fmt.Printf("Original: '%s'\n", sentence)
    fmt.Printf("Title case: '%s'\n", titleCase)
    
    // 11. Find and replace multiple patterns
    text2 := "Hello, World! How are you, World?"
    replacements := map[string]string{
        "Hello": "Hi",
        "World": "Universe",
        "How": "Where",
    }
    
    result := replaceMultiple(text2, replacements)
    fmt.Printf("Original: '%s'\n", text2)
    fmt.Printf("Replaced: '%s'\n", result)
    
    // 12. Extract numbers from string
    text3 := "The price is $19.99 and the tax is $2.50"
    numbers := extractNumbers(text3)
    fmt.Printf("Numbers in '%s': %v\n", text3, numbers)
    
    // 13. Extract emails from string
    text4 := "Contact us at admin@example.com or support@company.org"
    emails := extractEmails(text4)
    fmt.Printf("Emails in '%s': %v\n", text4, emails)
    
    // 14. String similarity
    str3 := "kitten"
    str4 := "sitting"
    str5 := "hello"
    str6 := "world"
    
    fmt.Printf("Similarity between '%s' and '%s': %d\n", str3, str4, levenshteinDistance(str3, str4))
    fmt.Printf("Similarity between '%s' and '%s': %d\n", str5, str6, levenshteinDistance(str5, str6))
    
    // 15. String rotation
    original := "abcdef"
    rotated := rotateString(original, 2)
    fmt.Printf("Original: '%s'\n", original)
    fmt.Printf("Rotated by 2: '%s'\n", rotated)
    
    // 16. String to title case (proper)
    text5 := "the quick brown fox jumps over the lazy dog"
    properTitle := toTitleCase(text5)
    fmt.Printf("Original: '%s'\n", text5)
    fmt.Printf("Proper title: '%s'\n", properTitle)
    
    // 17. Remove punctuation
    text6 := "Hello, World! How are you today?"
    noPunct := removePunctuation(text6)
    fmt.Printf("Original: '%s'\n", text6)
    fmt.Printf("No punctuation: '%s'\n", noPunct)
    
    // 18. String to slug
    title := "Hello World! This is a Test"
    slug := toSlug(title)
    fmt.Printf("Title: '%s'\n", title)
    fmt.Printf("Slug: '%s'\n", slug)
    
    // 19. String masking
    creditCard := "1234567890123456"
    masked := maskString(creditCard, 4, '*')
    fmt.Printf("Credit card: '%s'\n", creditCard)
    fmt.Printf("Masked: '%s'\n", masked)
    
    // 20. String validation patterns
    patterns := map[string]string{
        "email":    `^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$`,
        "phone":    `^\d{3}-\d{3}-\d{4}$`,
        "zipcode":  `^\d{5}(-\d{4})?$`,
        "url":      `^https?://[^\s/$.?#].[^\s]*$`,
    }
    
    testStrings := map[string]string{
        "email":    "user@example.com",
        "phone":    "123-456-7890",
        "zipcode":  "12345-6789",
        "url":      "https://www.example.com",
    }
    
    for pattern, testStr := range testStrings {
        regex := regexp.MustCompile(patterns[pattern])
        isValid := regex.MatchString(testStr)
        fmt.Printf("'%s' matches %s pattern: %t\n", testStr, pattern, isValid)
    }
}

// Helper functions for advanced string operations

func isPalindrome(s string) bool {
    runes := []rune(s)
    for i, j := 0, len(runes)-1; i < j; i, j = i+1, j-1 {
        if runes[i] != runes[j] {
            return false
        }
    }
    return true
}

func areAnagrams(s1, s2 string) bool {
    if len(s1) != len(s2) {
        return false
    }
    
    count1 := make(map[rune]int)
    count2 := make(map[rune]int)
    
    for _, r := range s1 {
        count1[r]++
    }
    
    for _, r := range s2 {
        count2[r]++
    }
    
    return mapsEqual(count1, count2)
}

func mapsEqual(m1, m2 map[rune]int) bool {
    if len(m1) != len(m2) {
        return false
    }
    
    for k, v := range m1 {
        if m2[k] != v {
            return false
        }
    }
    
    return true
}

func generatePermutations(s string) []string {
    if len(s) <= 1 {
        return []string{s}
    }
    
    var permutations []string
    
    for i, char := range s {
        rest := s[:i] + s[i+1:]
        subPerms := generatePermutations(rest)
        
        for _, perm := range subPerms {
            permutations = append(permutations, string(char)+perm)
        }
    }
    
    return permutations
}

func longestCommonPrefix(strs []string) string {
    if len(strs) == 0 {
        return ""
    }
    
    prefix := strs[0]
    
    for i := 1; i < len(strs); i++ {
        for !strings.HasPrefix(strs[i], prefix) {
            prefix = prefix[:len(prefix)-1]
            if prefix == "" {
                return ""
            }
        }
    }
    
    return prefix
}

func longestCommonSubstring(s1, s2 string) string {
    maxLen := 0
    endingIndex := 0
    
    dp := make([][]int, len(s1)+1)
    for i := range dp {
        dp[i] = make([]int, len(s2)+1)
    }
    
    for i := 1; i <= len(s1); i++ {
        for j := 1; j <= len(s2); j++ {
            if s1[i-1] == s2[j-1] {
                dp[i][j] = dp[i-1][j-1] + 1
                if dp[i][j] > maxLen {
                    maxLen = dp[i][j]
                    endingIndex = i
                }
            }
        }
    }
    
    return s1[endingIndex-maxLen : endingIndex]
}

func compressString(s string) string {
    if len(s) == 0 {
        return ""
    }
    
    var compressed strings.Builder
    count := 1
    
    for i := 1; i < len(s); i++ {
        if s[i] == s[i-1] {
            count++
        } else {
            compressed.WriteString(string(s[i-1]))
            if count > 1 {
                compressed.WriteString(fmt.Sprintf("%d", count))
            }
            count = 1
        }
    }
    
    // Add the last character
    compressed.WriteString(string(s[len(s)-1]))
    if count > 1 {
        compressed.WriteString(fmt.Sprintf("%d", count))
    }
    
    return compressed.String()
}

func decompressString(s string) string {
    var decompressed strings.Builder
    
    i := 0
    for i < len(s) {
        char := s[i]
        i++
        
        // Parse the number
        numStr := ""
        for i < len(s) && isDigit(rune(s[i])) {
            numStr += string(s[i])
            i++
        }
        
        count := 1
        if numStr != "" {
            count, _ = strconv.Atoi(numStr)
        }
        
        // Repeat the character
        for j := 0; j < count; j++ {
            decompressed.WriteString(string(char))
        }
    }
    
    return decompressed.String()
}

func isDigit(r rune) bool {
    return r >= '0' && r <= '9'
}

func wordFrequency(text string) map[string]int {
    words := strings.Fields(strings.ToLower(text))
    frequencies := make(map[string]int)
    
    for _, word := range words {
        // Remove punctuation
        cleanWord := strings.TrimFunc(word, func(r rune) bool {
            return !unicode.IsLetter(r) && !unicode.IsDigit(r)
        })
        
        if cleanWord != "" {
            frequencies[cleanWord]++
        }
    }
    
    return frequencies
}

func removeDuplicateWords(s string) string {
    words := strings.Fields(s)
    seen := make(map[string]bool)
    var unique []string
    
    for _, word := range words {
        if !seen[word] {
            seen[word] = true
            unique = append(unique, word)
        }
    }
    
    return strings.Join(unique, " ")
}

func titleCase(s string) string {
    words := strings.Fields(s)
    for i, word := range words {
        if len(word) > 0 {
            words[i] = strings.ToUpper(string(word[0])) + strings.ToLower(word[1:])
        }
    }
    
    return strings.Join(words, " ")
}

func replaceMultiple(s string, replacements map[string]string) string {
    result := s
    
    for old, new := range replacements {
        result = strings.ReplaceAll(result, old, new)
    }
    
    return result
}

func extractNumbers(s string) []string {
    regex := regexp.MustCompile(`\d+\.?\d*`)
    return regex.FindAllString(s, -1)
}

func extractEmails(s string) []string {
    regex := regexp.MustCompile(`[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}`)
    return regex.FindAllString(s, -1)
}

func levenshteinDistance(s1, s2 string) int {
    m := len(s1)
    n := len(s2)
    
    dp := make([][]int, m+1)
    for i := range dp {
        dp[i] = make([]int, n+1)
    }
    
    for i := 0; i <= m; i++ {
        dp[i][0] = i
    }
    
    for j := 0; j <= n; j++ {
        dp[0][j] = j
    }
    
    for i := 1; i <= m; i++ {
        for j := 1; j <= n; j++ {
            if s1[i-1] == s2[j-1] {
                dp[i][j] = dp[i-1][j-1]
            } else {
                dp[i][j] = 1 + min(dp[i-1][j], dp[i][j-1], dp[i-1][j-1])
            }
        }
    }
    
    return dp[m][n]
}

func min(a, b, c int) int {
    if a < b && a < c {
        return a
    } else if b < c {
        return b
    }
    return c
}

func rotateString(s string, positions int) string {
    runes := []rune(s)
    n := len(runes)
    
    if n == 0 {
        return s
    }
    
    positions = positions % n
    if positions < 0 {
        positions += n
    }
    
    rotated := make([]rune, n)
    
    for i := 0; i < n; i++ {
        newPos := (i + positions) % n
        rotated[newPos] = runes[i]
    }
    
    return string(rotated)
}

func toTitleCase(s string) string {
    words := strings.Fields(s)
    
    for i, word := range words {
        // Skip short words (articles, prepositions, conjunctions)
        shortWords := map[string]bool{
            "a": true, "an": true, "the": true,
            "and": true, "but": true, "or": true, "nor": true,
            "at": true, "by": true, "for": true, "from": true,
            "in": true, "into": true, "of": true, "on": true,
            "onto": true, "out": true, "over": true, "to": true,
            "up": true, "with": true, "as": true, "per": true,
        }
        
        lowerWord := strings.ToLower(word)
        if i == 0 || !shortWords[lowerWord] {
            words[i] = strings.Title(lowerWord)
        } else {
            words[i] = lowerWord
        }
    }
    
    return strings.Join(words, " ")
}

func removePunctuation(s string) string {
    var result strings.Builder
    
    for _, r := range s {
        if unicode.IsLetter(r) || unicode.IsDigit(r) || unicode.IsSpace(r) {
            result.WriteRune(r)
        }
    }
    
    return result.String()
}

func toSlug(s string) string {
    // Convert to lowercase
    s = strings.ToLower(s)
    
    // Replace spaces with hyphens
    s = strings.ReplaceAll(s, " ", "-")
    
    // Remove non-alphanumeric characters except hyphens
    var result strings.Builder
    
    for _, r := range s {
        if unicode.IsLetter(r) || unicode.IsDigit(r) || r == '-' {
            result.WriteRune(r)
        }
    }
    
    // Remove consecutive hyphens
    slug := result.String()
    for strings.Contains(slug, "--") {
        slug = strings.ReplaceAll(slug, "--", "-")
    }
    
    // Remove leading/trailing hyphens
    slug = strings.Trim(slug, "-")
    
    return slug
}

func maskString(s string, visible int, maskChar rune) string {
    runes := []rune(s)
    
    if len(runes) <= visible {
        return s
    }
    
    for i := 0; i < len(runes)-visible; i++ {
        runes[i] = maskChar
    }
    
    return string(runes)
}
```

## String Formatting

### Printf-style Formatting
```go
package main

import "fmt"

func main() {
    // Printf-style formatting
    
    // 1. Basic formatting
    name := "John"
    age := 30
    height := 1.75
    
    fmt.Printf("Name: %s, Age: %d, Height: %.2f\n", name, age, height)
    
    // 2. Width and padding
    fmt.Printf("Right-aligned: |%10s|\n", "Hello")
    fmt.Printf("Left-aligned: |%-10s|\n", "Hello")
    fmt.Printf("Zero-padded: |%010d|\n", 42)
    
    // 3. Precision
    pi := 3.14159265359
    fmt.Printf("Pi with 2 decimals: %.2f\n", pi)
    fmt.Printf("Pi with 4 decimals: %.4f\n", pi)
    fmt.Printf("Pi scientific: %e\n", pi)
    
    // 4. Different bases
    number := 255
    fmt.Printf("Decimal: %d\n", number)
    fmt.Printf("Binary: %b\n", number)
    fmt.Printf("Octal: %o\n", number)
    fmt.Printf("Hexadecimal: %x\n", number)
    fmt.Printf("Hexadecimal (uppercase): %X\n", number)
    
    // 5. Pointers
    value := 42
    ptr := &value
    fmt.Printf("Value: %d\n", value)
    fmt.Printf("Pointer: %p\n", ptr)
    fmt.Printf("Pointer value: %v\n", *ptr)
    
    // 6. Verbs
    fmt.Printf("Default verb: %v\n", name)
    fmt.Printf("Default verb (struct): %+v\n", struct{X int}{X: 42})
    fmt.Printf("Go syntax: %#v\n", struct{X int}{X: 42})
    fmt.Printf("Type: %T\n", name)
    fmt.Printf("Boolean: %t\n", true)
    
    // 7. Custom formatting with interfaces
    person := Person{Name: "Alice", Age: 25}
    fmt.Printf("Person: %s\n", person)
    fmt.Printf("Person (debug): %+v\n", person)
    
    // 8. String building with fmt.Sprintf
    formatted := fmt.Sprintf("User: %s, Age: %d, Active: %t", name, age, true)
    fmt.Printf("Formatted: %s\n", formatted)
    
    // 9. Error formatting
    err := fmt.Errorf("error code %d: %s", 404, "Not Found")
    fmt.Printf("Error: %v\n", err)
    
    // 10. Table formatting
    headers := []string{"Name", "Age", "City"}
    data := [][]string{
        {"John", "30", "New York"},
        {"Alice", "25", "Boston"},
        {"Bob", "35", "Chicago"},
    }
    
    fmt.Println("User Table:")
    printTable(headers, data)
    
    // 11. Number formatting with commas
    largeNumber := 1234567
    fmt.Printf("Large number: %s\n", formatWithCommas(largeNumber))
    
    // 12. Percentage formatting
    percentage := 0.7543
    fmt.Printf("Percentage: %.2f%%\n", percentage*100)
    
    // 13. Currency formatting
    amount := 1234.56
    fmt.Printf("Currency: $%.2f\n", amount)
    
    // 14. Time formatting
    now := time.Now()
    fmt.Printf("Current time: %s\n", now.Format("2006-01-02 15:04:05"))
    fmt.Printf("Time with timezone: %s\n", now.Format(time.RFC3339))
    
    // 15. File size formatting
    sizes := []int64{1024, 1048576, 1073741824, 1099511627776}
    for _, size := range sizes {
        fmt.Printf("Size %d: %s\n", size, formatFileSize(size))
    }
    
    // 16. Multiple arguments
    fmt.Printf("Multiple args: %s %d %.2f %t\n", "test", 42, 3.14, true)
    
    // 17. Reusing format strings
    userFormat := "User: %s, Age: %d, Email: %s"
    fmt.Printf(userFormat+"\n", "John", 30, "john@example.com")
    fmt.Printf(userFormat+"\n", "Alice", 25, "alice@example.com")
    
    // 18. Conditional formatting
    score := 85
    grade := "F"
    if score >= 90 {
        grade = "A"
    } else if score >= 80 {
        grade = "B"
    } else if score >= 70 {
        grade = "C"
    } else if score >= 60 {
        grade = "D"
    }
    
    fmt.Printf("Score: %d, Grade: %s\n", score, grade)
    
    // 19. Alignment and spacing
    items := []struct {
        Name  string
        Price float64
        Qty   int
    }{
        {"Apple", 1.99, 5},
        {"Banana", 0.99, 10},
        {"Cherry", 2.49, 3},
    }
    
    fmt.Println("Inventory:")
    fmt.Printf("%-10s %8s %6s\n", "Item", "Price", "Qty")
    fmt.Println(strings.Repeat("-", 26))
    
    for _, item := range items {
        fmt.Printf("%-10s $%7.2f %6d\n", item.Name, item.Price, item.Qty)
    }
    
    // 20. Debug formatting
    debug := struct {
        ID     int
        Name   string
        Active bool
        Data   map[string]interface{}
    }{
        ID:     123,
        Name:   "Test",
        Active: true,
        Data:   map[string]interface{}{"key": "value", "number": 42},
    }
    
    fmt.Printf("Debug info: %+v\n", debug)
}

type Person struct {
    Name string
    Age  int
}

func (p Person) String() string {
    return fmt.Sprintf("%s (%d years old)", p.Name, p.Age)
}

func printTable(headers []string, data [][]string) {
    // Print headers
    for i, header := range headers {
        fmt.Printf("%-15s", header)
        if i < len(headers)-1 {
            fmt.Printf(" | ")
        }
    }
    fmt.Println()
    
    // Print separator
    separator := strings.Repeat("-", 15)
    for i := range headers {
        fmt.Printf("%s", separator)
        if i < len(headers)-1 {
            fmt.Printf("-+-")
        }
    }
    fmt.Println()
    
    // Print data
    for _, row := range data {
        for i, cell := range row {
            fmt.Printf("%-15s", cell)
            if i < len(headers)-1 {
                fmt.Printf(" | ")
            }
        }
        fmt.Println()
    }
}

func formatWithCommas(n int) string {
    s := fmt.Sprintf("%d", n)
    
    // Insert commas every 3 digits from the right
    for i := len(s) - 3; i > 0; i -= 3 {
        s = s[:i] + "," + s[i:]
    }
    
    return s
}

func formatFileSize(bytes int64) string {
    const unit = 1024
    
    if bytes < unit {
        return fmt.Sprintf("%d B", bytes)
    }
    
    exp := int64(0)
    value := float64(bytes)
    
    for value >= unit && exp < 4 {
        value /= unit
        exp++
    }
    
    units := []string{"B", "KB", "MB", "GB", "TB"}
    return fmt.Sprintf("%.1f %s", value, units[exp])
}
```

## Summary

Go strings provide:

**String Basics:**
- Immutable sequences of bytes
- UTF-8 encoded by default
- Efficient for text processing
- Rich built-in operations
- Unicode support

**String Operations:**
- Concatenation and joining
- Case conversion
- Trimming and padding
- Splitting and joining
- Searching and replacing
- Counting and validation

**Advanced Manipulation:**
- Palindrome and anagram checking
- Permutation generation
- Pattern matching with regex
- Compression and decompression
- Frequency analysis
- Similarity algorithms

**String Formatting:**
- Printf-style formatting
- Width and precision control
- Different base representations
- Custom formatting interfaces
- Table and report formatting
- Localization support

**Key Features:**
- Immutable nature (thread-safe)
- Efficient memory usage
- Built-in Unicode support
- Rich standard library functions
- Performance optimized

**Best Practices:**
- Use strings.Builder for concatenation
- Prefer fmt.Sprintf for formatting
- Handle Unicode properly
- Consider memory efficiency
- Use appropriate string functions

**Common Use Cases:**
- Text processing and parsing
- Data validation and sanitization
- Logging and reporting
- Configuration management
- User interface text

Go's string handling provides a powerful, efficient, and safe way to work with text data, with excellent Unicode support and comprehensive standard library functions for common operations.
