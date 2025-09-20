public function up()
{
    Schema::create('answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('submission_id')->constrained()->onDelete('cascade');
        $table->foreignId('field_id')->constrained()->onDelete('cascade');
        $table->text('value')->nullable();
        $table->timestamps();
    });
}
