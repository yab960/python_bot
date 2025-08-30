from telegram import Update, ReplyKeyboardMarkup, KeyboardButton
from telegram.ext import Application, CommandHandler, ContextTypes
import os
import asyncio

# Bot token and webhook URL from environment variables
BOT_TOKEN = os.getenv('BOT_TOKEN')
WEBHOOK_URL = os.getenv('WEBHOOK_URL')

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    # Create a button to request the phone number
    button = KeyboardButton(text="Share Phone Number", request_contact=True)
    custom_keyboard = [[button]]
    reply_markup = ReplyKeyboardMarkup(custom_keyboard, one_time_keyboard=True)

    # Send welcome message with the button
    await update.message.reply_text(
        "Welcome! Please share your phone number.",
        reply_markup=reply_markup
    )

async def main() -> None:
    # Create the Application with the bot token
    application = Application.builder().token(BOT_TOKEN).build()

    # Register the /start command handler
    application.add_handler(CommandHandler("start", start))

    # Set up webhook
    await application.initialize()
    await application.start()
    await application.updater.start_webhook(
        listen="0.0.0.0",
        port=int(os.getenv("PORT", 8443)),
        url_path="/webhook",
        webhook_url=f"{WEBHOOK_URL}/webhook"
    )
    print(f"Bot is running with webhook at {WEBHOOK_URL}/webhook")

if __name__ == '__main__':
    asyncio.run(main())